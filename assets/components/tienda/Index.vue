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
              :archiveName="`tienda`"
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
        editTitle: "Editar producto tienda",
      });
  
      const sortBy = ["id", "nombre", "pvp"];
  
      const headers = [
        { text: "ID", value: "id", sortable: true },
        { text: "Nombre", value: "name", sortable: true },
        { text: "Precio", value: "pvp", sortable: true },
        { text: "Editar/Eliminar", value: "operation" },
      ];
      const itemsSelect = ref([
        { name: "Buscar id", value: "id" },
        { name: "Buscar Nombre", value: "name" },
        { name: "Buscar Pvp", value: "pvp" },
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
      const tiendaValue = document
        .querySelector("[data-tienda]")
        .getAttribute("data-tienda");
      items.value = JSON.parse(tiendaValue);
  
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
  