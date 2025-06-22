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
              :archiveName="`statscomensales`"
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
        { text: "Fecha", value: "fecha", sortable: true },
        { text: "Total Comensales", value: "total_comensales", sortable: true },
      ];
      const itemsSelect = ref([
        { name: "Buscar id", value: "id" },
        { name: "Buscar Fecha", value: "fecha" },
        { name: "Buscar por total", value: "total_comensales" },
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
      const statsComensalesValue = document
        .querySelector("[data-statscomensales]")
        .getAttribute("data-statscomensales");
      items.value = JSON.parse(statsComensalesValue);
  
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
  