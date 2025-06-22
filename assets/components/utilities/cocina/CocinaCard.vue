<template>
  <template v-if="pedidoRef.length > 0 && headerStatus != 2 && show == true">
    <v-card
      v-if="pedidoRef.length > 0 && headerStatus != 2"
      min-width="300"
      max-width="344"
      class="ma-2 card-cocina"
    >
      <v-card-item
        class="card-header pa-1"
        :class="{
          green: headerStatus == 1,
          red: headerStatus == 0,
        }"
      >
        <v-card-title> Mesa {{ pedidoRef[0]?.mesa }} </v-card-title>
        <v-card-subtitle> {{ contador }} </v-card-subtitle>
        <v-card-subtitle>Hora del pedido {{ horaInicio }} </v-card-subtitle>
      </v-card-item>
      <div>
        <ul class="ul">
          <div v-if="pedidos.primer.length > 0" class="primer">
            <span class="p-4">1°</span>
            <br />
            <li
              @click="togglePedidoStatus(item)"
              class="li"
              v-for="(item, index) of pedidos.primer"
              :class="{
                'd-flex':
                  item.num_plato == 1 &&
                  pedidoStatus[item.id] != 4 &&
                  !item.entregado,
                'd-none':
                  item.num_plato != 1 ||
                  pedidoStatus[item.id] == 4 ||
                  item.entregado,
                'li-red':
                  (item.estado_plato == 0 &&
                    item.estado_plato == pedidoStatus[item.id]) ||
                  pedidoStatus[item.id] == 0,
                'li-yellow':
                  (item.estado_plato == 1 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 1 &&
                    pedidoStatus[item.id] >= item.estado_plato),
                'li-green':
                  (item.estado_plato == 2 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 2 &&
                    pedidoStatus[item.id] >= item.estado_plato),
                'li-blue':
                  (item.estado_plato == 3 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 3 &&
                    pedidoStatus[item.id] >= item.estado_plato),
              }"
              :key="index"
            >
              <div
                class="d-flex justify-space-between w-100"
                v-if="!item.is_bebida"
              >
                <div class="">
                  {{ item.comida }}
                  <span v-if="item?.count > 1" class="multi">
                    {{ item.count > 1 ? ` x${item.count}` : "" }}
                  </span>
                </div>
                <div class="d-flex justify-center align-center cont-circulo">
                  <div
                    class="d-flex justify-center align-center circulo"
                    :class="{
                      contRed:
                        (item.estado_plato == 0 &&
                          item.estado_plato == pedidoStatus[item.id]) ||
                        pedidoStatus[item.id] == 0,
                      contYellow:
                        (item.estado_plato == 1 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 1 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                      contGreen:
                        (item.estado_plato == 2 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 2 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                      contBlue:
                        (item.estado_plato == 3 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 3 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                    }"
                  >
                    <span
                      class="status d-flex align-center"
                      v-if="
                        (item.estado_plato == 0 &&
                          item.estado_plato == pedidoStatus[item.id]) ||
                        pedidoStatus[item.id] == 0
                      "
                      >R</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 1 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 1 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >E</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 2 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 2 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >L</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 3 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 3 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >M</span
                    >
                  </div>
                </div>
              </div>
            </li>
          </div>
          <div v-if="pedidos.segundo.length > 0" class="segundo">
            <span class="p-4">2°</span>
            <li
              @click="togglePedidoStatus(item)"
              class="li"
              v-for="(item, index) of pedidos.segundo"
              :class="{
                'd-flex':
                  item.num_plato == 2 &&
                  pedidoStatus[item.id] != 4 &&
                  !item.entregado,
                'd-none':
                  item.num_plato != 2 ||
                  pedidoStatus[item.id] == 4 ||
                  item.entregado,
                'li-red':
                  (item.estado_plato == 0 &&
                    item.estado_plato == pedidoStatus[item.id]) ||
                  pedidoStatus[item.id] == 0,
                'li-yellow':
                  (item.estado_plato == 1 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 1 &&
                    pedidoStatus[item.id] >= item.estado_plato),
                'li-green':
                  (item.estado_plato == 2 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 2 &&
                    pedidoStatus[item.id] >= item.estado_plato),
                'li-blue':
                  (item.estado_plato == 3 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 3 &&
                    pedidoStatus[item.id] >= item.estado_plato),
              }"
              :key="index"
            >
              <div
                class="d-flex justify-space-between w-100"
                v-if="!item.is_bebida"
              >
                <div class="">
                  {{ item.comida }}
                  <span v-if="item?.count > 1" class="multi">
                    {{ item.count > 1 ? ` x${item.count}` : "" }}
                  </span>
                </div>
                <div class="d-flex justify-center align-center cont-circulo">
                  <div
                    class="d-flex justify-center align-center circulo"
                    :class="{
                      contRed:
                        (item.estado_plato == 0 &&
                          item.estado_plato == pedidoStatus[item.id]) ||
                        pedidoStatus[item.id] == 0,
                      contYellow:
                        (item.estado_plato == 1 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 1 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                      contGreen:
                        (item.estado_plato == 2 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 2 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                      contBlue:
                        (item.estado_plato == 3 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 3 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                    }"
                  >
                    <span
                      class="status d-flex align-center"
                      v-if="
                        (item.estado_plato == 0 &&
                          item.estado_plato == pedidoStatus[item.id]) ||
                        pedidoStatus[item.id] == 0
                      "
                      >R</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 1 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 1 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >E</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 2 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 2 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >L</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 3 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 3 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >M</span
                    >
                  </div>
                </div>
              </div>
            </li>
          </div>
          <div v-if="pedidos.tercer.length > 0" class="tercer">
            <span class="p-4">3°</span>
            <br />
            <li
              @click="togglePedidoStatus(item)"
              class="li"
              v-for="(item, index) of pedidos.tercer"
              :class="{
                'd-flex':
                  item.num_plato == 3 &&
                  pedidoStatus[item.id] != 4 &&
                  !item.entregado,
                'd-none':
                  item.num_plato != 3 ||
                  pedidoStatus[item.id] == 4 ||
                  item.entregado,
                'li-red':
                  (item.estado_plato == 0 &&
                    item.estado_plato == pedidoStatus[item.id]) ||
                  pedidoStatus[item.id] == 0,
                'li-yellow':
                  (item.estado_plato == 1 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 1 &&
                    pedidoStatus[item.id] >= item.estado_plato),
                'li-green':
                  (item.estado_plato == 2 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 2 &&
                    pedidoStatus[item.id] >= item.estado_plato),
                'li-blue':
                  (item.estado_plato == 3 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 3 &&
                    pedidoStatus[item.id] >= item.estado_plato),
              }"
              :key="index"
            >
              <div
                class="d-flex justify-space-between w-100"
                v-if="!item.is_bebida"
              >
                <div class="">
                  {{ item.comida }}
                  <span v-if="item?.count > 1" class="multi">
                    {{ item.count > 1 ? ` x${item.count}` : "" }}
                  </span>
                </div>
                <div class="d-flex justify-center align-center cont-circulo">
                  <div
                    class="d-flex justify-center align-center circulo"
                    :class="{
                      contRed:
                        (item.estado_plato == 0 &&
                          item.estado_plato == pedidoStatus[item.id]) ||
                        pedidoStatus[item.id] == 0,
                      contYellow:
                        (item.estado_plato == 1 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 1 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                      contGreen:
                        (item.estado_plato == 2 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 2 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                      contBlue:
                        (item.estado_plato == 3 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 3 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                    }"
                  >
                    <span
                      class="status d-flex align-center"
                      v-if="
                        (item.estado_plato == 0 &&
                          item.estado_plato == pedidoStatus[item.id]) ||
                        pedidoStatus[item.id] == 0
                      "
                      >R</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 1 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 1 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >E</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 2 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 2 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >L</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 3 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 3 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >M</span
                    >
                  </div>
                </div>
              </div>
            </li>
          </div>
          <div v-if="pedidos.postre.length > 0" class="postre">
            <span class="p-4">Postre</span>
            <br />
            <li
              @click="togglePedidoStatus(item)"
              class="li"
              v-for="(item, index) of pedidos.postre"
              :class="{
                'd-flex':
                  item.num_plato == 4 &&
                  pedidoStatus[item.id] != 4 &&
                  !item.entregado,
                'd-none':
                  item.num_plato != 4 ||
                  pedidoStatus[item.id] == 4 ||
                  item.entregado,
                'li-red':
                  (item.estado_plato == 0 &&
                    item.estado_plato == pedidoStatus[item.id]) ||
                  pedidoStatus[item.id] == 0,
                'li-yellow':
                  (item.estado_plato == 1 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 1 &&
                    pedidoStatus[item.id] >= item.estado_plato),
                'li-green':
                  (item.estado_plato == 2 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 2 &&
                    pedidoStatus[item.id] >= item.estado_plato),
                'li-blue':
                  (item.estado_plato == 3 &&
                    pedidoStatus[item.id] == item.estado_plato) ||
                  (pedidoStatus[item.id] == 3 &&
                    pedidoStatus[item.id] >= item.estado_plato),
              }"
              :key="index"
            >
              <div
                class="d-flex justify-space-between w-100"
                v-if="!item.is_bebida"
              >
                <div class="">
                  {{ item.comida }}
                  <span v-if="item?.count > 1" class="multi">
                    {{ item.count > 1 ? ` x${item.count}` : "" }}
                  </span>
                </div>
                <div class="d-flex justify-center align-center cont-circulo">
                  <div
                    class="d-flex justify-center align-center circulo"
                    :class="{
                      contRed:
                        (item.estado_plato == 0 &&
                          item.estado_plato == pedidoStatus[item.id]) ||
                        pedidoStatus[item.id] == 0,
                      contYellow:
                        (item.estado_plato == 1 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 1 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                      contGreen:
                        (item.estado_plato == 2 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 2 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                      contBlue:
                        (item.estado_plato == 3 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 3 &&
                          pedidoStatus[item.id] >= item.estado_plato),
                    }"
                  >
                    <span
                      class="status d-flex align-center"
                      v-if="
                        (item.estado_plato == 0 &&
                          item.estado_plato == pedidoStatus[item.id]) ||
                        pedidoStatus[item.id] == 0
                      "
                      >R</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 1 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 1 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >E</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 2 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 2 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >L</span
                    >
                    <span
                      class="status d-flex align-center"
                      v-else-if="
                        (item.estado_plato == 3 &&
                          pedidoStatus[item.id] == item.estado_plato) ||
                        (pedidoStatus[item.id] == 3 &&
                          pedidoStatus[item.id] >= item.estado_plato)
                      "
                      >M</span
                    >
                  </div>
                </div>
              </div>
            </li>
          </div>
        </ul>
      </div>
    </v-card>
  </template>
</template>

<script>
import { ref, triggerRef, computed } from "vue";
//Store
import { useStatusStore } from "./../../../store/statusStore.js";
//Composables
import useCocina from "./../../../composables/cocina/useCocina.js";
export default {
  setup(props) {
    const { mesa } = props;
    const contador = ref(0);
    const pedidoStatus = ref([]);
    const headerStatus = ref(0);
    const horaInicio = ref("");
    const color = ref("green");
    const pedidoRef = ref([]);
    const pedidos = ref({
      primer: [],
      segundo: [],
      tercer: [],
      postre: [],
    });
    const show = ref(false);
    const isWaiting = ref(false);

    //Store
    const statusStore = useStatusStore();
    //Composables
    const { setPedidoEntregado, setNewPedidoStatus, imprimirPlatoListo } =
      useCocina();

    const formatSeconds = (seconds) => {
      const mins = Math.floor(seconds / 60);
      const secs = seconds % 60;
      return `${mins}:${secs < 10 ? "0" : ""}${secs}`;
    };

    const formattedCounter = computed(() => formatSeconds(contador.value));

    const setAllStatus = () => {
      let pedidoStat = pedidoStatus.value;
      for (let pedido of pedidoRef.value) {
        if (pedidoStat[pedido.id] == undefined) {
          pedidoStat[pedido.id] = 0;
        }
        if (pedidoStat[pedido.id] == 4 && !pedido.marchando) {
          pedidoStat[pedido.id] = 5;
        }
        if (!isWaiting.value) {
          if (pedido.estado_plato >= pedidoStat[pedido.id]) {
            pedidoStat[pedido.id] = pedido.estado_plato;
          }
        }
      }
      pedidoStatus.value = pedidoStat;
    };

    const checkearEnviados = () => {
      if (pedidoRef.value.length > 0) {
        for (let item of pedidoRef.value) {
          if (item?.entregado != true) {
            show.value = true;
          }
        }
      }
    };

    const incrementarContador = () => {
      setTimeout(() => {
        for (let item of statusStore.pedidos) {
          if (item[0].id_mesa == mesa) {
            pedidoRef.value = item;
          }
        }

        let i = 0;
        let pedidoStat = pedidoStatus.value;
        if (pedidoRef.value.length > 0) {
          for (let item of pedidoRef.value) {
            if (pedidoStat[item.id] == 0 || pedidoStat[item.id] == 1) {
              i = 1;
            }
          }
        } else {
          headerStatus.value = 2;
        }
        let j = 0;

        pedidos.value = { primer: [], segundo: [], tercer: [], postre: [] };

        if (pedidoRef.value.length > 0) {
          for (let item of pedidoRef.value) {
            if (
              pedidoStat[item.id] == 0 ||
              pedidoStat[item.id] == 1 ||
              pedidoStat[item.id] == 2 ||
              pedidoStat[item.id] == 3
            ) {
              j = 1;
            }
            if (item.num_plato == 1) {
              pedidos.value.primer.push(item);
            }
            if (item.num_plato == 2) {
              pedidos.value.segundo.push(item);
            }
            if (item.num_plato == 3) {
              pedidos.value.tercer.push(item);
            }
            if (item.num_plato == 4) {
              pedidos.value.postre.push(item);
            }
          }
        }

        if (i == 1) {
          headerStatus.value = 0;
        } else if (i == 0) {
          headerStatus.value = 1;
        }
        if (j == 0) {
          headerStatus.value = 2;
        }

        setAllStatus();
        incrementarContador();
      }, 3000);
    };

    const setHoraInicio = () => {
      horaInicio.value = `${new Date().getHours()}:${new Date().getMinutes()}`;
    };

    const togglePedidoStatus = async (item) => {
      console.log("inicio", pedidoStatus.value[item.id]);
      if (item.estado_plato <= pedidoStatus.value[item.id]) {
        item.estado_plato = pedidoStatus.value[item.id];
      }
      console.log("fin", pedidoStatus.value[item.id]);
      if (pedidoStatus.value[item.id] == 0) {
        isWaiting.value = true;
        pedidoStatus.value[item.id] = 1;
        await setNewPedidoStatus(item, 1);
        isWaiting.value = false;
      } else if (pedidoStatus.value[item.id] == 1) {
        isWaiting.value = true;
        pedidoStatus.value[item.id] = 2;
        await Promise.all([
          imprimirPlatoListo(item),
          setNewPedidoStatus(item, 2),
        ]);
        isWaiting.value = false;
      } else if (pedidoStatus.value[item.id] == 2) {
        isWaiting.value = true;
        pedidoStatus.value[item.id] = 3;
        await setNewPedidoStatus(item, 3);
        isWaiting.value = false;
      } else if (pedidoStatus.value[item.id] == 3) {
        isWaiting.value = true;
        pedidoStatus.value[item.id] = 4;
        setPedidoEntregado(item);
        await setNewPedidoStatus(item, 4);
        isWaiting.value = false;
      } else if (pedidoStatus.value[item.id] == 4) {
        pedidoStatus.value[item.id] = 0;
        setPedidoEntregado(item);
      }
    };

    setHoraInicio();
    incrementarContador();

    const incrementar = () => {
      setTimeout(() => {
        for (let item of statusStore.pedidos) {
          if (item[0].id_mesa == mesa) {
            contador.value = contador.value + 1;
          }
        }

        checkearEnviados();
        incrementar();
      }, 1000);
    };

    incrementar();

    return {
      contador,
      mesa,
      statusStore,
      pedidoStatus,
      horaInicio,
      contador: formattedCounter,
      headerStatus,
      pedidoStatus,
      color,
      show,
      pedidoRef,
      pedidos,
      togglePedidoStatus,
    };
  },
  props: {
    mesa: {
      type: Number,
      required: false,
    },
  },
};
</script>

<style scoped lang="scss">
.card-cocina {
  .card-header {
    display: flex;
    justify-content: space-between;
  }
  .ul {
    list-style: none;
    padding: 10px;
    color: black;
    .li {
      padding: 10px;
      box-shadow: 5px 3px 5px -1px rgba(142, 146, 142, 0.75);
      -webkit-box-shadow: 5px 3px 5px -1px rgba(142, 146, 142, 0.75);
      -moz-box-shadow: 5px 3px 5px -1px rgba(142, 146, 142, 0.75);
      border-radius: 5px;
      margin-bottom: 4px;
      margin-right: 4px;
      margin-left: 4px;
      cursor: pointer;
    }
    .li-white {
      box-shadow: 5px 3px 5px -1px rgba(232, 276, 222, 0.75);
      -webkit-box-shadow: 5px 3px 5px -1px rgba(232, 276, 222, 0.75);
      -moz-box-shadow: 5px 3px 5px -1px rgba(232, 276, 222, 0.75);
    }
    .li-green {
      box-shadow: 5px 3px 5px -1px rgba(32, 176, 122, 0.75);
      -webkit-box-shadow: 5px 3px 5px -1px rgba(32, 176, 122, 0.75);
      -moz-box-shadow: 5px 3px 5px -1px rgba(32, 176, 122, 0.75);
    }
    .li-yellow {
      box-shadow: 5px 3px 5px -1px rgba(202, 232, 38, 0.75);
      -webkit-box-shadow: 5px 3px 5px -1px rgba(202, 232, 38, 0.75);
      -moz-box-shadow: 5px 3px 5px -1px rgba(202, 232, 38, 0.75);
    }
    .li-red {
      box-shadow: 5px 3px 5px -1px rgba(255, 0, 0, 0.75);
      -webkit-box-shadow: 5px 3px 5px -1px rgba(255, 0, 0, 0.75);
      -moz-box-shadow: 5px 3px 5px -1px rgba(255, 0, 0, 0.75);
    }
    .li-blue {
      box-shadow: 5px 3px 5px -1px rgba(0, 117, 255, 0.75);
      -webkit-box-shadow: 5px 3px 5px -1px rgba(0, 117, 255, 0.75);
      -moz-box-shadow: 5px 3px 5px -1px rgba(0, 117, 255, 0.75);
    }
  }
}

.cont-circulo {
  .circulo {
    width: 27px;
    height: 27px;
    border-radius: 50px;
    .status {
    }
  }
}

.contWhite {
  animation: whiteAnimation 1488ms infinite ease-out;
}

.contRed {
  animation: redAnimation 1488ms infinite ease-out;
}

.contYellow {
  animation: yellowAnimation 1488ms infinite ease-out;
}

.contBlue {
  animation: blueAnimation 1488ms infinite ease-out;
}

.contGreen {
  animation: greenAnimation 1488ms infinite ease-out;
}

.red {
  background: rgba(182, 43, 43, 0.9);
  color: white;
}
.green {
  background: rgba(12, 193, 53, 0.9);
  color: white;
}
.yellow {
  background: orange;
  color: white;
}
.blue {
  background: blue;
  color: white;
}
.multi {
  padding: 5px;
  border-radius: 50%;
  background: orange;
  color: white;
  border: 1px solid orange;
}

.primer {
  border-radius: 7px;
  margin-bottom: 10px;
  box-shadow: 4px 4px 17px -1px rgba(52, 183, 183, 0.8);
  -webkit-box-shadow: 4px 4px 17px -1px rgba(52, 183, 183, 0.8);
  -moz-box-shadow: 4px 4px 17px -1px rgba(52, 183, 183, 0.8);
}
.segundo {
  border-radius: 7px;
  margin-bottom: 10px;
  box-shadow: 4px 4px 17px -1px rgba(52, 93, 153, 0.8);
  -webkit-box-shadow: 4px 4px 17px -1px rgba(52, 93, 153, 0.8);
  -moz-box-shadow: 4px 4px 17px -1px rgba(52, 93, 153, 0.8);
}
.tercer {
  border-radius: 7px;
  margin-bottom: 10px;
  box-shadow: 4px 4px 17px -1px rgba(182, 103, 103, 0.8);
  -webkit-box-shadow: 4px 4px 17px -1px rgba(182, 103, 103, 0.8);
  -moz-box-shadow: 4px 4px 17px -1px rgba(52, 183, 183, 0.8);
}
.postre {
  border-radius: 7px;
  margin-bottom: 10px;
  box-shadow: 4px 4px 17px -1px rgba(192, 23, 133, 0.4);
  -webkit-box-shadow: 4px 4px 17px -1px rgba(192, 23, 133, 0.4);
  -moz-box-shadow: 4px 4px 17px -1px rgba(192, 23, 133, 0.4);
}

@keyframes whiteAnimation {
  from {
    background-color: rgba(212, 253, 253, 1);
    color: rgba(152, 153, 153, 1);
  }

  50% {
    background-color: rgba(132, 138, 131, 0.5);
    color: white;
  }

  to {
    background-color: rgba(212, 253, 253, 1);
    color: rgba(152, 153, 153, 1);
  }
}

@keyframes redAnimation {
  from {
    background-color: rgba(212, 53, 53, 1);
    color: white;
  }

  50% {
    background-color: rgba(222, 78, 131, 0.5);
    color: rgba(212, 53, 53, 1);
  }

  to {
    background-color: rgba(212, 53, 53, 1);
    color: white;
  }
}

@keyframes yellowAnimation {
  from {
    background-color: rgba(230, 221, 67, 1);
    color: white;
  }

  50% {
    background-color: rgba(222, 179, 78, 0.2);
    color: rgba(250, 201, 67, 1);
  }

  to {
    background-color: rgba(230, 221, 67, 1);
    color: white;
  }
}

@keyframes greenAnimation {
  from {
    background-color: rgba(53, 212, 106, 1);
    color: white;
  }

  50% {
    background-color: rgba(78, 222, 147, 0.5);
    color: rgba(53, 212, 106, 1);
  }

  to {
    background-color: rgba(53, 212, 106, 1);
    color: white;
  }
}

@keyframes blueAnimation {
  from {
    background-color: rgba(63, 112, 206, 1);
    color: white;
  }

  50% {
    background-color: rgba(68, 112, 207, 0.5);
    color: rgba(53, 52, 206, 1);
  }

  to {
    background-color: rgba(63, 112, 206, 1);
    color: white;
  }
}
</style>
