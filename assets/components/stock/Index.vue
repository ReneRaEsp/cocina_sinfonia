<template>
  <v-content>
    <v-container class="elevation-2 mb-4 rounded" fluid>
      <v-row>
        <v-col style="height: auto" cols="12" md="6" class="bg-light">
          <v-col
            style="height: auto"
            cols="12"
            class="d-flex justify-center align-center bg-light"
          >
            <v-row class="d-flex justify-center align-center">
              <v-col class="d-flex justify-center" cols="12" sm="4">
                <v-btn @click="setProv('prov')" class="bg-light" elevation="2"
                  >Top Proveedores</v-btn
                >
              </v-col>
              <v-col class="d-flex justify-center" cols="12" sm="4">
                <v-btn @click="setProv('prod')" class="bg-light" elevation="2"
                  >Top Productos</v-btn
                >
              </v-col>
              <v-col class="d-flex justify-center" cols="12" sm="4">
                <v-btn @click="setProv('tipos')" class="bg-light" elevation="2"
                  >Top Tipos de Comida</v-btn
                >
              </v-col>
            </v-row>
          </v-col>
          <v-col
            style="height: auto"
            cols="12"
            class="bg-light d-flex justify-content-center align-center flex-column"
            v-if="isProv === 'prov'"
          >
            <h2 color="black text-align-center">Top 5 Mayores Proveedores</h2>
            <DoughnutChart :chartData="provData" />
          </v-col>
          <v-col
            style="height: auto"
            cols="12"
            class="bg-light d-flex justify-content-center align-center flex-column"
            v-if="isProv === 'prod'"
          >
            <h2 color="black text-align-center">Top 5 Mayores Productos</h2>
            <DoughnutChart :chartData="prodData" />
          </v-col>
          <v-col
            style="height: auto"
            cols="12"
            class="bg-light d-flex justify-content-center align-center flex-column"
            v-if="isProv === 'tipos'"
          >
            <h2 color="black text-align-center">Top 5 Tipos de Comida</h2>
            <DoughnutChart :chartData="tiposData" />
          </v-col>
        </v-col>
        <v-col cols="12" md="6" class="bg-light">
          <Table
            style="min-height: 400px; height: auto"
            :headers="headers"
            :items="items"
            :itemsSelect="itemsSelect"
            :initialSearchField="initialSearchField"
            :sortBy="sortBy"
            :archiveName="`stock`"
            :titles="modalTitles"
          />
        </v-col>
      </v-row>
    </v-container>
  </v-content>
</template>

<script>
import { ref } from "vue";
import { DoughnutChart, BarChart } from "vue-chart-3";
import { Chart, registerables } from "chart.js";
Chart.register(...registerables);
//Components
import Table from "./../utilities/Table.vue";
//Composables
import useStockChart from "./../../composables/useStockChart";
export default {
  setup() {
    //Variables

    const isProv = ref("prov");
    const modalTitles = ref({
      addTitle: "Añadir producto al stock",
      editTitle: "Editar producto",
    });

    const sortBy = ["id", "name", "amount", "p_id"];

    const headers = [
      { text: "ID", value: "id", sortable: true },
      { text: "Nombre", value: "name", sortable: true },
      //{ text: "Proveedor", value: "p_id", sortable: true },
      { text: "Descripción", value: "tf_id" },
      { text: "Cantidad", value: "amount", sortable: true },
      { text: "Editar/Eliminar", value: "operation" },
    ];
    const itemsSelect = ref([
      { name: "Buscar id", value: "id" },
      { name: "Buscar Nombre", value: "name" },
      { name: "Buscar Cantidad", value: "amount" },
      //{ name: "Buscar Proveedor", value: "p_id" },
    ]);
    const initialSearchField = ref("name");

    //Composables
    const {
      items,
      provData,
      prodData,
      tiposData,
      setProvData,
      setProdData,
      setTopItemsType,
    } = useStockChart();

    //Main
    const stockValue = document
      .querySelector("[data-stock]")
      .getAttribute("data-stock");
    items.value = JSON.parse(stockValue);

    const setProv = (newTop) => {
      isProv.value = newTop;
    };

    setProvData();
    setProdData();
    setTopItemsType();

    return {
      headers,
      items,
      modalTitles,
      sortBy,
      itemsSelect,
      initialSearchField,
      provData,
      prodData,
      tiposData,
      isProv,
      setProv,
    };
  },
  components: {
    DoughnutChart,
    BarChart,
    Table,
  },
};
</script>

<style lang="scss"></style>
