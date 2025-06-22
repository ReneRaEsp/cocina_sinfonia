import { ref, triggerRef } from "vue";

const useVentas = () => {
  const pedidosLista = ref([]);
  const cantidad = ref({
    name: "Últimos 3 meses",
    value: 3,
  });
  const cantidades = ref([
    {
      name: "Últimos 3 meses",
      value: 3,
    },
    {
      name: "Últimos 4 meses",
      value: 4,
    },
    {
      name: "Últimos 6 meses",
      value: 6,
    },
    {
      name: "Últimos 12 meses",
      value: 12,
    },
    {
      name: "Últimos 24 meses",
      value: 24,
    },
  ]);
  const obtenerMesesAnteriores = (cantidad) => {
    let mesesAnteriores = [];
    let fecha = new Date();
    let mes = fecha.getMonth() + 1;
    let anio = fecha.getFullYear();
    let fechaFormateada = (mes < 10 ? "0" : "") + mes + "/" + anio;

    mesesAnteriores.push(fechaFormateada);

    for (let i = 0; i < cantidad; i++) {
      // Puedes ajustar el número de meses que deseas obtener
      fecha.setMonth(fecha.getMonth() - 1);

      mes = fecha.getMonth() + 1; // Se suma 1 ya que los meses van de 0 a 11
      anio = fecha.getFullYear();

      // Ajustar el año si el mes es enero
      if (mes === 0) {
        mes = 12;
        anio -= 1;
      }

      fechaFormateada = (mes < 10 ? "0" : "") + mes + "/" + anio;
      mesesAnteriores.push(fechaFormateada);
    }
    mesesAnteriores = mesesAnteriores.reverse();
    return mesesAnteriores;
  };

  const dateToMonthAndYear = (fecha) => {
    let mes = fecha.split("/")[0];
    let year = fecha.split("/")[1];
    switch (mes) {
      case "01":
        mes = "Enero";
        break;
      case "02":
        mes = "Febrero";
        break;
      case "03":
        mes = "Marzo";
        break;
      case "04":
        mes = "Abril";
        break;
      case "05":
        mes = "Mayo";
        break;
      case "06":
        mes = "Junio";
        break;
      case "07":
        mes = "Julio";
        break;
      case "08":
        mes = "Agosto";
        break;
      case "09":
        mes = "Septiembre";
        break;
      case "10":
        mes = "Octubre";
        break;
      case "11":
        mes = "Noviembre";
        break;
      case "12":
        mes = "Diciembre";
        break;
    }
    return { mes, year };
  };

  const sumarCantidadesPorMes = (transactions, mes) => {
    let total = 0;
    transactions = transactions.filter((transaction) =>
      transaction.fecha.endsWith(mes)
    );
    for (let transaction of transactions) {
      total = total + transaction.cantidad;
    }
    let fecha = dateToMonthAndYear(mes);
    return { fecha, total: parseFloat(total.toFixed(2)) };
  };

  const checkPedidos = async (pedidos) => {
    const URL = `/listrefs?pedidos=${pedidos}`;
    const OPTIONS = {
      method: "GET",
    };
    try {
      let res = await fetch(URL, OPTIONS);
      res = await res.json();
      pedidosLista.value = res.historial;
    } catch (e) {
      console.log(e);
    }
  };

  return {
    cantidad,
    cantidades,
    pedidosLista,
    obtenerMesesAnteriores,
    sumarCantidadesPorMes,
    checkPedidos,
  };
};

export default useVentas;
