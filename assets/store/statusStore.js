import { defineStore } from "pinia";

export const useStatusStore = defineStore("statusStore", {
  state: () => ({
    pedidosStatus: [],
    pedidosHeader: [],
    pedidos: [],
  }),
  actions: {
    setPedidosStatus(mesa, position) {
      let pedidosStatus = this.pedidosStatus;
      switch (pedidosStatus[mesa]) {
        case undefined:
          pedidosStatus[mesa] = [];
          break;
      }
      switch (pedidosStatus[mesa][position]) {
        case undefined:
          pedidosStatus[mesa][position] = 0;
          break;
      }

      this.pedidosStatus = pedidosStatus;
    },
    setPedidosHeader(mesa, newStatus) {
      let pedidosHeader = this.pedidosHeader;
      pedidosHeader[mesa] = newStatus;
      this.pedidosHeader = pedidosHeader;
    },
    togglePedidoStatus(mesa, position) {
      let pedidosStatus = this.pedidosStatus;
      if (pedidosStatus[mesa][position] == 3) {
        pedidosStatus[mesa][position] = 0;
      } else {
        pedidosStatus[mesa][position] = pedidosStatus[mesa][position] + 1;
      }
      this.pedidosStatus = pedidosStatus;
    },
    setPedidoStatus(mesa, position, newEstado) {
      let pedidosStatus = this.pedidosStatus;
      pedidosStatus[mesa][position] = newEstado;
      this.pedidosStatus = pedidosStatus;
    },
    async listAllPedidos() {
      const URL = `/listallpedidos`;
      const OPTIONS = {
        method: "GET",
      };
      try {
        let res = await fetch(URL, OPTIONS);
        res = await res.json();
        const { pedidos } = res;

        const pedidosAgrupados = pedidos.reduce((agrupados, pedido) => {
          const { mesa } = pedido;

          // Si la clave de la mesa aún no existe en el objeto agrupado, la inicializamos con un array vacío
          if (!agrupados[mesa]) {
            agrupados[mesa] = [];
          }

          // Añadimos el pedido al array correspondiente a la mesa
          agrupados[mesa].push(pedido);

          return agrupados;
        }, {});

        this.pedidos = Object.values(pedidosAgrupados);

        function detectarDuplicados(datos) {
          let counts = {};
          let ids = {};
          for (let i = 0; i < datos.length; i++) {
            let comida = datos[i].comida + datos[i].num_plato;
            let id = datos[i].id;
            if (datos[i].entregado == false) {
              if (counts[comida]) {
                counts[comida] = counts[comida] + 1;
              } else {
                counts[comida] = 1;
              }
              ids[comida] = ids[comida] ? [...ids[comida], id] : [id];
            }
          }
          let uniqueArray = datos.filter(
            (obj, index, self) =>
              index ===
              self.findIndex(
                (t) => t.comida + t.num_plato === obj.comida + obj.num_plato
              )
          );
          uniqueArray.forEach((obj) => {
            obj.count = counts[obj.comida + obj.num_plato];
            obj.ids = ids[obj.comida + obj.num_plato];
          });
          return uniqueArray;
        }

        function filtrarDatos(datos) {
          let filtrados = [];
          let filtrado = [];
          for (let item of datos) {
            item = detectarDuplicados(item);
            for (let it of item) {
              if (it.is_comida && it.marchando && it.estado == 0) {
                filtrado.push(it);
              }
            }
            filtrado.length != 0 ? filtrados.push(filtrado) : null;
            filtrado = [];
          }

          return { filtrados };
        }

        this.pedidos = this.pedidos.map((pedido) => {
          pedido = pedido.filter((item) => {
            return item.entregado == false;
          });
          return pedido;
        });

        const datosFiltrados = filtrarDatos(this.pedidos);
        this.pedidos = datosFiltrados.filtrados;
      } catch (e) {
        console.log(e);
      }
    },
  },
});
