<template>
  <v-content>
    <v-container class="d-flex justify-center elevation-2 mb-4 rounded" fluid>
      <v-col cols="12" md="12" class="bg-light">
        <Table
          style="min-height: 400px; height: auto"
          :headers="headers"
          :items="items"
          :itemsSelect="itemsSelect"
          :initialSearchField="initialSearchField"
          :sortBy="sortBy"
          :archiveName="`caja`"
        />
      </v-col>
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
      { text: "Día", value: "dia", sortable: true },
      { text: "Inicio", value: "inicio", sortable: true },
      { text: "Final", value: "final" },
      { text: "Total Calculado", value: "totalcalculado" },
      { text: "Descuadre", value: "descuadre" },
      { text: "Observaciones", value: "observaciones" },
    ];
    const itemsSelect = ref([
      { name: "Buscar Id", value: "id" },
      { name: "Buscar Día", value: "dia" },
      { name: "Buscar Inicio", value: "inicio" },
      { name: "Buscar Final", value: "final" },
      { name: "Buscar Total Calculado", value: "totalcalculado" },
      { name: "Buscar Descuadre", value: "descuadre" },
      { name: "Buscar Observaciones", value: "observaciones" },
    ]);
    const initialSearchField = ref("dia");

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
    const cajaValue = document
      .querySelector("[data-caja]")
      .getAttribute("data-caja");
    items.value = JSON.parse(cajaValue);

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
