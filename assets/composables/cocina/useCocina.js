import { ref, triggerRef } from "vue";

const useCocina = () => {
  const pedidosRef = ref([]);
  const pedidoRef = ref([]);

  const setPedidoEntregado = async (ids) => {
    let pedido = {};

    if (!ids?.ids) {
      ids = [ids.id];
    } else {
      ids = ids.ids;
    }

    const URL = `/setpedidoentregado`;
    const OPTIONS = {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        ids,
      }),
    };
    try {
      let res = await fetch(URL, OPTIONS);
      res = await res.json();
    } catch (e) {
      console.log(e);
    }
    return pedido;
  };

  const imprimirPlatoListo = async (item) => {
    const URL = `/imprimirplatolisto`;
    const OPTIONS = {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        mesa: item.mesa,
        nombreplato: item.comida,
        cantidad: item.count,
      }),
    };

    try {
      let res = await fetch(URL, OPTIONS);
      res = await res.json();
    } catch (e) {
      console.log(e);
    }
  };

  const setNewPedidoStatus = async (item, newStatus) => {
    const URL = `/setPedidoStatus`;
    const OPTIONS = {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: item.id,
        newEstado: newStatus,
      }),
    };
    try {
      let res = await fetch(URL, OPTIONS);
      res = await res.json();
    } catch (e) {
      console.log(e);
    }
  };

  return {
    //Variables
    pedidosRef,
    pedidoRef,
    //Functions
    setPedidoEntregado,
    setNewPedidoStatus,
    imprimirPlatoListo,
  };
};

export default useCocina;
