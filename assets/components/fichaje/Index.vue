<template>
  <v-content>
    <v-container class="elevation-2 mb-4 rounded">
      <v-row>
        <v-col style="height: auto" cols="12" class="bg-light">
          <Table
            style="min-height: 400px; height: auto"
            :headers="headers"
            :items="items"
            :itemsSelect="itemsSelect"
            :initialSearchField="initialSearchField"
            :sortBy="sortBy"
            :archiveName="`fichajes`"
          />
        </v-col>
      </v-row>
    </v-container>
  </v-content>
</template>

<script setup>
import { ref } from "vue";
//Components
import Table from "./../utilities/Table.vue";

const items = ref([]);

const sortBy = ["id", "usuario", "fecha"];

const headers = [
  { text: "ID", value: "id", sortable: true },
  { text: "Usuario", value: "user", sortable: true },
  { text: "Fecha", value: "fecha", sortable: true },
  { text: "Inicio AM", value: "inicio_am" },
  { text: "Fin AM", value: "fin_am" },
  { text: "Inicio PM", value: "inicio_pm" },
  { text: "Fin PM", value: "fin_pm" },
];

const itemsSelect = ref([
  { name: "Buscar id", value: "id" },
  { name: "Buscar Usuario", value: "user" },
  { name: "Buscar Fecha", value: "fecha" },
]);

const initialSearchField = ref("user");

//Main
const fichajesValue = document
  .querySelector("[data-fichajes]")
  .getAttribute("data-fichajes");
items.value = JSON.parse(fichajesValue);

items.value = items.value.map((item) => ({
  ...item,
  fecha: item.fecha.date.split(" ")[0],
  inicio_am: item.inicio_am.date.split(" ")[0],
  fin_am: item.fin_am.date.split(" ")[0],
  inicio_pm: item.inicio_pm.date.split(" ")[0],
  fin_pm: item.fin_pm.date.split(" ")[0],
}));
</script>

<style></style>
