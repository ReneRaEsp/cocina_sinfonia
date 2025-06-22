<template>
  <v-row>
    <v-col cols="12">
        <div class="d-flex justify-space-between align-center">
          <span>Fecha: {{ pedidosLista[0]?.fecha?.date?.split(".")[0] }}</span>
          <span><v-btn
                  class="elevation-2"
                  color="bg-light"
                  text="Crear Factura"
                  icon="mdi-file-document"
                  variant="flat"
                  @click="fetchDataAndGeneratePDF"
                ></v-btn>
          </span>
      </div> 
    </v-col>
    <v-col cols="12">
      <span>Mesa: {{ pedidosLista[0]?.mesa }}</span>
    </v-col>
    <v-col cols="12">
      <span>Comensales: {{ pedidosLista[0]?.comensales }}</span>
    </v-col>
    <v-col cols="12">
      <span>Atendido por: {{ pedidosLista[0]?.user }}</span>
    </v-col>
    <v-col cols="12">
      --------------------------------------------------------------
    </v-col>
  </v-row>
  <v-row>
    <v-col
      cols="11"
      v-for="pedido of pedidosLista"
      :key="pedido"
      class="d-flex justify-content-between contVentasModal"
    >
      <div></div>
      <div class="pedido">
        {{ pedido.comida }}..........................{{
          pedido.precio_total == 0 ? "Invitación" : pedido.precio_total + "€"
        }}
      </div>
    </v-col>
  </v-row>

  <v-row>
    <v-col cols="12">
      ------------------------------------------------------------
    </v-col>
    <v-col class="d-flex justify-content-between contVentasModal" cols="10">
      <div></div>
      <div>Euros {{ total.toFixed(2) }}</div>
    </v-col>
    <v-col cols="12">
      ------------------------------------------------------------
    </v-col>
  </v-row>

   <!-- Aquí va tu modal -->
   <v-dialog v-model="modalVisible" max-width="600">
    <v-card>
      <v-card-title>Ingresar Datos</v-card-title>
      <v-card-text>
        <v-text-field v-model="nombre" label="Nombre" required></v-text-field>
        <v-text-field v-model="cif" label="CIF" type="text" maxlength="9" required></v-text-field>
        <v-text-field v-model="telefono" label="Teléfono" type="tel" required></v-text-field>
        <v-text-field v-model="direccion" label="Dirección" type="text" required></v-text-field>
        <v-text-field v-model="email" id="email-customer" label="Email" type="email" required :rules="emailRules"></v-text-field>
      </v-card-text>
      <v-card-actions>
        <v-btn @click="confirmarDatos">Confirmar</v-btn>
      </v-card-actions>
    </v-card>

    <!-- Snackbar para mostrar mensaje de error -->
    <v-snackbar v-model="snackbarVisible" color="error" top>
      Por favor, complete todos los campos.
      <v-btn color="white" text @click="snackbarVisible = false">Cerrar</v-btn>
    </v-snackbar>
  </v-dialog>

  <!-- Snackbar para mostrar mensaje de error -->
  <v-snackbar v-model="snackbarSuccess" color="success" top>
      El correo se ha enviado correctamente.
      <v-btn color="white" text @click="snackbarSuccess = false">Cerrar</v-btn>
    </v-snackbar>

    <!-- Snackbar para mostrar mensaje de error -->
    <v-snackbar v-model="snackbarError" color="error" top>
      Esta factura ya existe.
      <v-btn color="white" text @click="snackbarError = false">Cerrar</v-btn>
    </v-snackbar>
</template>

<script>
import { ref, watch  } from "vue";
import useVentas from "./../../../composables/ventas/useVentas";
import jsPDF from "jspdf";
import jsPDFInvoiceTemplate, { OutputType } from "jspdf-invoice-template-nodejs";
import axios from 'axios';
import Swal from 'sweetalert2';
export default {
  data() {
    return {
      valid: false,
      email: '',
      emailRules: [
        v => !!v || 'Email es requerido',
        v => /.+@.+\..+/.test(v) || 'El formato de email no es correcto'
      ]
    };
  },
  setup(props) {
    const total = ref(0);
    const { pedidos } = props;
    var datos;
    const modalVisible = ref(false);
    const snackbarVisible = ref(false);
    const snackbarSuccess = ref(false);
    const snackbarError = ref(false);
    const nombre = ref('');
    const cif = ref('');
    const telefono = ref('');
    const direccion = ref('');
    const email = ref('');
    let totalValue = ref(0);
    let porcentaje21 = ref(0);
    let restante = ref(0);
    let valor21Porciento = ref(0);
    let valorRestante = ref(0);
    const datosConfirmados = ref(false);

    const { pedidosLista, checkPedidos } = useVentas();




    const calcularTotal = async () => {
      await checkPedidos(pedidos);
      for (let pedido of pedidosLista.value) {
        total.value = total.value + pedido.precio_total;
      }

    totalValue = total.value;

    console.log(pedidosLista);

    // Calcular el 21% del total

    if(pedidosLista._rawValue[0].iva === 21){
      porcentaje21 = totalValue * 0.21;

    } else {

      porcentaje21 = totalValue * 0.10;
    }
    

    // Calcular el restante después de quitar el 21%
    restante = totalValue - porcentaje21;

    valor21Porciento = porcentaje21;
    valorRestante = restante;
    };

    calcularTotal();

    const fetchDataAndGeneratePDF = async () => {
      try {

        // Mostrar el modal para ingresar los datos
        modalVisible.value = true;

        // Esperar a que el usuario confirme los datos
        await esperarConfirmacionDatos();

      const URL = `/datosempresa`;
      const OPTIONS = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
      };
      //console.log(`Editar`);
      let res = await fetch(URL, OPTIONS);
      res = await res.json();

      datos = res.datos

        // Generar el PDF
        generatePDF();
    } catch (e) {
      console.log(e);
    }
    };

    const confirmarDatos = () => {

      if (!nombre.value || !cif.value || !telefono.value || !direccion.value || !email.value) {
        // Mostrar el snackbar con el mensaje de error
        snackbarVisible.value = true;

        console.log(snackbarVisible)
      } else {
        // Cerrar el modal y marcar los datos como confirmados
        modalVisible.value = false;
        datosConfirmados.value = true;
      }
    };

    const esperarConfirmacionDatos = () => {
      return new Promise((resolve, reject) => {
        // Crear un watcher para esperar hasta que los datos sean confirmados
        const stop = watch(datosConfirmados, (nuevoValor) => {
          if (nuevoValor) {
            stop(); // Dejar de observar
            resolve();
          }
        });
      });
    };

    const uploadPDF = async (pdfBlob, num_ref, date, email) => {

    var responseStatus;
    // Crear un FormData y agregar el Blob del PDF
    const formData = new FormData();
    formData.append('file', pdfBlob, 'factura.pdf');
    formData.append('num_ref', num_ref);
    formData.append('fecha', date);
    formData.append('email', email);

    try {
        // Enviar la solicitud POST al backend
        const response = await axios.post('/guardarpdf', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        responseStatus = response.data;

        



    } catch (error) {
        console.error('Error al subir el PDF:', error);
    }

    if(responseStatus.error){
          snackbarError.value = true ;

          console.log(snackbarError);

        } else {

          snackbarSuccess.value = true;
        }
};

    const generatePDF = async () => {
      const ahora = new Date(pedidosLista._rawValue[0].fecha.date);
      const dia = String(ahora.getDate()).padStart(2, '0');
      const mes = String(ahora.getMonth() + 1).padStart(2, '0'); // Los meses en JavaScript van de 0 a 11
      const año = ahora.getFullYear();

      // Formato DD/MM/YYYY
      const fechaPersonalizada = `${dia}/${mes}/${año}`;

   // Crear el objeto cantidadPorComida para acumular las cantidades de cada comida
    const cantidadPorComida = {};

    // Llenar el objeto cantidadPorComida con las cantidades de cada comida
    pedidosLista.value.forEach((pedido) => {
      const { comida, precio_total, precio } = pedido;

      // Si la comida ya está en cantidadPorComida, incrementa su cantidad
      if (cantidadPorComida[comida]) {
        cantidadPorComida[comida].cantidad++;
        cantidadPorComida[comida].precio_total += precio_total;
        cantidadPorComida[comida].precio = precio;
      } else {
        // Si no existe, inicializa la cantidad en 1 y el precio_total
        cantidadPorComida[comida] = {
          cantidad: 1,
          precio_total: precio_total,
          precio: precio
        };
      }
    });

    // Crear el array de table basado en cantidadPorComida
    const table = Object.keys(cantidadPorComida).map((comida, index) => [
      index + 1,
      comida,
      cantidadPorComida[comida].precio === 0 ? "Invitación" : `${cantidadPorComida[comida].precio}`,
      cantidadPorComida[comida].cantidad, // Cantidad acumulada de la comida
      cantidadPorComida[comida].precio_total === 0 ? "Invitación" : `${cantidadPorComida[comida].precio_total}`,
    ]);

      const props = {
        outputType: 'blob',
        returnJsPDFDocObject: true,
        fileName: "Factura",
        orientationLandscape: false,
        compress: true,
        logo: {
            src: datos.logo,
            type: 'PNG', // optional, when src= data:uri (nodejs case)
            width: 40, // aspect ratio = width/height
            height: 40,
            margin: {
                top: 0, // negative or positive num, from the current position
                left: 0, // negative or positive num, from the current position
            }
        },
        business: {
            name: datos.nombre,
            address: datos.direccion,
            phone: datos.telefono,
            email: datos.email,
        },
        contact: {
            label: "Factura emitida para:",
            name: nombre.value.toString(),
            address: direccion.value.toString(),
            phone: telefono.value.toString(),
            email: email.value.toString(),
            otherInfo: cif.value.toString(),
        },
        invoice: {
            label: "Factura #: ",
            num: pedidosLista._rawValue[0].idfactura,
            invDate: "Fecha de la Factura:" + fechaPersonalizada,
            invGenDate: "Fecha de Emisión: " + fechaPersonalizada,
            headerBorder: false,
            tableBodyBorder: false,
            header: [
                { title: "#", style: { width: 10 } },
                { title: "Descripción", style: { width: 50 } },
                { title: "Precio", style: { width: 50 } },
                { title: "Cantidad", style: { width: 50 } },
                { title: "Total", style: { width: 50 } }
            ],
            table: table,
            additionalRows: [{
                col1: 'Base imponible:',
                col2: valorRestante.toFixed(2).toString(),
                col3: '€'
            }, 
            {
            col1: 'IVA '+pedidosLista._rawValue[0].iva+'%:',
            col2: valor21Porciento.toFixed(2).toString() ,
            col3: '€',
            style: {
                fontSize: 10 //optional, default 12
            }
          },
          {
              col1: 'TOTAL:',
              col2: total.value.toFixed(2).toString(),
              col3: '€',
              style: {
                  fontSize: 10 //optional, default 12
              }
          }],
            invDescLabel: "Nota",
            invDesc: "Esta es una factura generada automáticamente.",
        },
        footer: {
            text: "Gracias por su compra",
        },
        pageEnable: true,
        pageLabel: "Página ",
      };

      const pdfObject = jsPDFInvoiceTemplate(props);

    // Obtener el Blob del PDF
    const pdfBlob = pdfObject.blob;

    // Llamar a la función para enviar el PDF al backend
    await uploadPDF(pdfBlob, pedidosLista._rawValue[0].num_ref, pedidosLista._rawValue[0].fecha.date, email.value);
    };

    




    return {
      total,
      pedidos,
      pedidosLista,
      fetchDataAndGeneratePDF,
      modalVisible,
      nombre,
      cif,
      telefono,
      direccion,
      email,
      snackbarVisible,
      snackbarSuccess,
      snackbarError,
      confirmarDatos,
    };
  },
  props: {
    pedidos: {
      type: String,
    },
  },
};
</script>

<style lang="scss" scoped>
.contVentasModal {
  .pedido {
    font-size: 14px;
  }
}
</style>
