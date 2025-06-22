<template>
    <v-content>
      <v-container class="elevation-2 mb-4 rounded" fluid>
        <v-row>
          <v-col cols="12" md="12" class="bg-light">
            <Table
              style="min-height: 400px; height: auto"
              :headers="headers"
              :items="items"
              :itemsSelect="itemsSelect"
              :initialSearchField="initialSearchField"
              :sortBy="sortBy"
              :archiveName="`statsbebida`"
              :titles="modalTitle"
              :hasEdit="false"
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
  
      const sortBy = ["id", "nombre", "pvp"];
  
      const headers = [
        { text: "ID", value: "id", sortable: true },
        { text: "Nombre", value: "name", sortable: true },
        { text: "Fecha", value: "dia", sortable: true },
        { text: "Total", value: "total_registros", sortable: true },
      ];
      const itemsSelect = ref([
        { name: "Buscar id", value: "id" },
        { name: "Buscar Nombre", value: "name" },
        { name: "Buscar Fecha", value: "dia" },
        { name: "Buscar por total", value: "total_registros" },
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
      const statsBebidaValue = document
        .querySelector("[data-statsbebida]")
        .getAttribute("data-statsbebida");
      items.value = JSON.parse(statsBebidaValue);
  
      const setProv = (newTop) => {
        isProv.value = newTop;
      };
  
      setProvData();
      setProdData();
      setTopItemsType();
  
      return {
        headers,
        items,
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
  