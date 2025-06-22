<template>
  <v-content>
    <v-container class="elevation-2 mb-4 rounded" fluid>
      <v-row>
        <v-col cols="12" md="12" class="bg-light">
          <v-col
            style="height: auto"
            cols="12"
            class="d-flex justify-content-center bg-light"
          >
            <v-row class="d-flex flex-row justify-center align-center">
              <v-col class="d-flex justify-center" cols="12" sm="4">
                <v-btn @click="setChart('3')" class="bg-light" elevation="2">
                  Últimos 3 meses
                </v-btn>
              </v-col>
              <v-col class="d-flex justify-center" cols="12" sm="4">
                <v-btn @click="setChart('6')" class="bg-light" elevation="2">
                  Últimos 6 meses
                </v-btn>
              </v-col>
              <v-col class="d-flex justify-center" cols="12" sm="4">
                <v-btn @click="setChart('12')" class="bg-light" elevation="2">
                  Últimos 12 meses
                </v-btn>
              </v-col>
            </v-row>
          </v-col>
          <v-col v-if="chartType === '3'" cols="12" class="bg-light">
            <h4>Ventas de los últimos 3 meses</h4>
            <LineChart
              ref="linearRef"
              :chartData="threeChartData"
              @chart:render="setChartData"
              class="mt-6"
            />
          </v-col>
          <v-col v-if="chartType === '6'" cols="12" class="bg-light">
            <h4>Ventas de los últimos 3 meses</h4>
            <LineChart
              ref="linearRef"
              :chartData="sixChartData"
              @chart:render="setChartData"
              class="mt-6"
            />
          </v-col>
          <v-col v-if="chartType === '12'" cols="12" class="bg-light">
            <h4>Ventas de los últimos 3 meses</h4>
            <LineChart
              ref="linearRef"
              :chartData="twelveChartData"
              @chart:render="setChartData"
              class="mt-16"
            />
          </v-col>
        </v-col>
      </v-row>
    </v-container>
  </v-content>


  <v-content>
    <v-container class="elevation-2 mb-4 rounded" fluid>
      <v-row>
  <v-col cols="12" md="12" class="bg-light">
          <Modal />
          <v-col
            style="height: auto"
            cols="12"
            class="d-flex justify-content-center bg-light"
          >
            <v-row class="d-flex flex-row justify-center align-center">
              <v-col class="d-flex justify-center" cols="12" sm="4">
                <v-btn
                  @click="setTable('unidad')"
                  class="bg-light"
                  elevation="2"
                  >Ventas por unidad</v-btn
                >
              </v-col>
              <v-col class="d-flex justify-center" cols="12" sm="4">
                <v-col cols="auto">
                  <v-btn @click="setTable('dia')" class="bg-light" elevation="2"
                    >Ventas por dia</v-btn
                  >
                </v-col>
              </v-col>
            </v-row>
          </v-col>
          <v-col
            style="min-height: 900px"
            cols="12"
            class="bg-light"
            v-if="tableType === 'unidad'"
          >
            <h4>Ventas por unidad</h4>
            <Table
              style="min-height: 400px; height: 400px"
              :headers="headers"
              :items="items"
              :itemsSelect="itemsSelect"
              :initialSearchField="initialSearchField"
              :sortBy="sortBy"
              :archiveName="`ventas`"
            />
          </v-col>
          <v-col
            v-if="tableType === 'dia'"
            style="min-height: 900px"
            cols="12"
            class="bg-light"
          >
            <h4>Ventas por dia</h4>
            <Table
              style="min-height: 400px; height: 400px"
              :headers="sumadosHeaders"
              :items="sumadosItems"
              :itemsSelect="sumadosItemsSelect"
              :initialSearchField="sumadosInitialSearchField"
              :sortBy="sumadosSortBy"
              :archiveName="`ventas`"
            />
          </v-col>
        </v-col>

      </v-row>
    </v-container>
  </v-content>
</template>

<script>
import { ref } from "vue";
import { LineChart } from "vue-chart-3";
import { Chart, registerables } from "chart.js";
Chart.register(...registerables);
//Components
import Table from "./../utilities/Table";
//Composables
import useVentas from "./../../composables/ventas/useVentas";
import useVentasTable from "./../../composables/ventas/useVentasTable";
export default {
  setup() {
    const threeMonths = ref([]);
    const sixMonths = ref([]);
    const twelveMonths = ref([]);
    const tableType = ref("dia");
    const chartType = ref("3");
    const modalTitles = ref({
      addTitle: "Añadir proveedor al stock",
      editTitle: "Editar proveedor",
    });

    const {
      items,
      sumadosItems,
      sortBy,
      sumadosSortBy,
      headers,
      sumadosHeaders,
      itemsSelect,
      sumadosItemsSelect,
      initialSearchField,
      sumadosInitialSearchField,
      //Functions
      sumarCantidadesPorFecha,
    } = useVentasTable();

    const ventasValue = document
      .querySelector("[data-ventas]")
      .getAttribute("data-ventas");
    items.value = JSON.parse(ventasValue);

    const setTable = (newTableType) => {
      tableType.value = newTableType;
    };

    const setChart = (newChartType) => {
      chartType.value = newChartType;
    };

    sumadosItems.value = sumarCantidadesPorFecha(items.value);

    //Composables
    const {
      cantidad,
      cantidades,
      linearRef,
      obtenerMesesAnteriores,
      sumarCantidadesPorMes,
    } = useVentas();

    let tresMesesAnteriores = obtenerMesesAnteriores(2);
    let seisMesesAnteriores = obtenerMesesAnteriores(5);
    let doceMesesAnteriores = obtenerMesesAnteriores(11);

    for (let i = 0; i < tresMesesAnteriores.length; i++) {
      threeMonths.value.push(
        sumarCantidadesPorMes(items.value, tresMesesAnteriores[i])
      );
    }

    for (let i = 0; i < seisMesesAnteriores.length; i++) {
      sixMonths.value.push(
        sumarCantidadesPorMes(items.value, seisMesesAnteriores[i])
      );
    }

    for (let i = 0; i < doceMesesAnteriores.length; i++) {
      twelveMonths.value.push(
        sumarCantidadesPorMes(items.value, doceMesesAnteriores[i])
      );
    }

    const threeMeses = threeMonths.value.map(
      (data) => data.fecha.mes + " " + data.fecha.year
    );
    const threeTotales = threeMonths.value.map((data) => data.total);

    const sixMeses = sixMonths.value.map(
      (data) => data.fecha.mes + " " + data.fecha.year
    );
    const sixTotales = sixMonths.value.map((data) => data.total);

    const twelveMeses = twelveMonths.value.map(
      (data) => data.fecha.mes + " " + data.fecha.year
    );
    const twelveTotales = twelveMonths.value.map((data) => data.total);

    const threeChartData = {
      title: "Gráfico de ventas de los Últimos meses",
      labels: threeMeses,
      datasets: [
        {
          label: "Total Ventas del mes",
          data: threeTotales,
        },
      ],
    };

    const sixChartData = {
      title: "Gráfico de ventas de los Últimos meses",
      labels: sixMeses,
      datasets: [
        {
          label: "Total Ventas del mes",
          data: sixTotales,
        },
      ],
    };

    const twelveChartData = {
      title: "Gráfico de ventas de los Últimos meses",
      labels: twelveMeses,
      datasets: [
        {
          label: "Total Ventas del mes",
          data: twelveTotales,
        },
      ],
    };

    return {
      items,
      threeChartData,
      sixChartData,
      twelveChartData,
      cantidad,
      cantidades,
      sumadosItems,
      sumadosItemsSelect,
      sumadosHeaders,
      sumadosSortBy,
      sumadosInitialSearchField,
      tableType,
      chartType,
      linearRef,
      sortBy,
      headers,
      modalTitles,
      itemsSelect,
      initialSearchField,
      setTable,
      setChart,
    };
  },
  components: { Table, LineChart },
};
</script>

<style></style>
