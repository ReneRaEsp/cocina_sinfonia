<template>
  <v-content>
    <v-container class="elevation-2 mb-4 rounded" fluid>
      <v-row>
        <v-col cols="auto" class="d-flex flex-wrap bg-light">
          <div
            v-for="(pedido, index) of mesas"
            :key="index"
            :draggable="false"
            @dragstart="handleDragStart(index)"
            @dragover="handleDragOver"
            @drop="handleDrop(index)"
          >
            <CocinaCard
              v-if="pedidos.includes(pedido.numero)"
              :alt="`${index}`"
              :mesa="pedido.id"
            />
          </div>
        </v-col>
      </v-row>
    </v-container>
  </v-content>
</template>

<script setup>
import { ref, triggerRef } from "vue";
//Store
import { useStatusStore } from "./../../store/statusStore.js";
//Composables
//Components
import CocinaCard from "./../utilities/cocina/CocinaCard.vue";

const draggedItem = ref(null);
const mesas = ref([]);
const pedidos = ref([]);
const statusStore = useStatusStore();

const handleDragStart = (index) => {
  draggedItem.value = index;
};
const handleDragOver = (event) => {
  event.preventDefault();
};
const handleDrop = (index) => {
  const droppedItem = mesas.value.splice(draggedItem.value, 1)[0];
  mesas.value.splice(index, 0, droppedItem);
  triggerRef(mesas);
  console.log(mesas.value);
  draggedItem.value = null;
};

const agruparPorId = (items) => {
  const mesas = new Set();
  items.forEach((subArray) => {
    subArray.forEach((obj) => {
      mesas.add(obj.mesa);
    });
  });

  return Array.from(mesas);
};

const listAllPedidosBucle = async () => {
  setTimeout(async () => {
    await statusStore.listAllPedidos();
    await listAllPedidosBucle();
    let pedidosDesordenados = statusStore.pedidos;
    pedidos.value = agruparPorId(pedidosDesordenados);
  }, 3000);
};

//Main
listAllPedidosBucle();
const mesaValue = document
  .querySelector("[data-cocina]")
  .getAttribute("data-cocina");

mesas.value = JSON.parse(mesaValue);
</script>

<style scoped lang="scss">
.cont-cocina {
  width: 100%;
  height: 200px;
  background: rgba(20, 190, 200, 0.9);
  color: blue;
}
.cont-cards {
  display: flex;

  .cocina-cards {
    display: flex;
    width: 20%;
    height: 20%;
    background: black;
    margin: 10px;
  }
}
</style>
