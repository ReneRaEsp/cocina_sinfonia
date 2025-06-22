import { ref, triggerRef } from "vue";

const useVentasTable = () => {
  const items = ref([]);
  const sumadosItems = ref([]);

  const sortBy = ["id", "mesa", "fecha", "cantidad", "metodo_pago"];
  const sumadosSortBy = ["id", "fecha", "total"];

  const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Mesa", value: "mesa", sortable: true },
    { text: "Ref Ticket", value: "refticket", sortable: true },
    { text: "Fecha", value: "fecha", sortable: true },
    { text: "Base imponible", value: "cantidad", sortable: true },
    { text: "IVA", value: "iva", sortable: true },
    { text: "Importe IVA", value: "importeiva", sortable: true },
    { text: "Método de pago", value: "metodo_pago", sortable: true },
    { text: "Observaciones", value: "observaciones", sortable: true },
    { text: "Nombre Factura", value: "name_factu", sortable: false },
    { text: "Ref", value: "ref", sortable: false },
  ];
  const sumadosHeaders = [
    { name: "ID", value: "id", sortable: true },
    { text: "Fecha", value: "fecha", sortable: true },
    { text: "Total Efectivo", value: "total_efectivo", sortable: true },
    { text: "Total Tarjeta", value: "total_tarjeta", sortable: true },
    { text: "Total", value: "total", sortable: true },
  ];

  const itemsSelect = ref([
    { name: "Buscar Id", value: "id" },
    { name: "Buscar Mesa", value: "mesa" },
    { name: "Buscar Fecha", value: "fecha" },
    { name: "Base imponible", value: "cantidad" },
    { name: "IVA", value: "iva" },
    { name: "Método de pago", value: "metodo_pago" },
    { name: "Ref", value: "ref" },
  ]);
  const sumadosItemsSelect = ref([
    { name: "Buscar Id", value: "id" },
    { name: "Buscar Fecha", value: "fecha" },
    { name: "Buscar Total", value: "total" },
  ]);
  const initialSearchField = ref("fecha");
  const sumadosInitialSearchField = ref("fecha");

  //Functions

  const sumarCantidadesPorFecha = (arrayOriginal) => {
    // Objeto para almacenar la suma de cantidades por fecha
    const sumaPorFecha = {};
    const tarjetaPorFecha = {};
    const efectivoPorFecha = {};
    let id = 1;

    // Iterar sobre el array original
    arrayOriginal.forEach((item) => {
      const { fecha, cantidad, metodo_pago } = item;

      // Verificar si ya existe una entrada para esa fecha en el objeto
      if (sumaPorFecha[fecha]) {
        // Si existe, sumar la cantidad
        sumaPorFecha[fecha] += cantidad;
      } else {
        // Si no existe, crear una nueva entrada
        sumaPorFecha[fecha] = cantidad;
      }
      if (tarjetaPorFecha[fecha]) {
        metodo_pago === "Tarjeta" ? (tarjetaPorFecha[fecha] += cantidad) : null;
      } else {
        metodo_pago === "Tarjeta" ? (tarjetaPorFecha[fecha] = cantidad) : null;
      }
      if (efectivoPorFecha[fecha]) {
        metodo_pago === "Efectivo"
          ? (efectivoPorFecha[fecha] += cantidad)
          : null;
      } else {
        metodo_pago === "Efectivo"
          ? (efectivoPorFecha[fecha] = cantidad)
          : null;
      }
    });
    const nuevoArray = Object.keys(sumaPorFecha).map((fecha) => ({
      id: id++,
      fecha: fecha,
      total_efectivo: efectivoPorFecha[fecha],
      total_tarjeta: tarjetaPorFecha[fecha],
      total: sumaPorFecha[fecha],
    }));

    return nuevoArray;
  };

  return {
    items,
    sumadosItems,
    sortBy,
    sumadosSortBy,
    headers,
    sumadosHeaders,
    itemsSelect,
    sumadosItemsSelect,
    initialSearchField,
    sumadosInitialSearchField,
    //Functions
    sumarCantidadesPorFecha,
  };
};

export default useVentasTable;
