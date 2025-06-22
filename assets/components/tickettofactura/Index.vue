<template>
    <v-content>
      <v-container class="elevation-2 mb-4 rounded" fluid>
        <v-row>
          <v-col style="height: 800px" cols="12" class="bg-light">
            <Table
              style="min-height: 400px; height: 400px"
              :headers="headers"
              :items="items"
              :itemsSelect="itemsSelect"
              :initialSearchField="initialSearchField"
              :sortBy="sortBy"
              :archiveName="`facturas`"
              :isDownloadable="false"
            />
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </template>
  
  <script>
  //Components
  import Table from "./../utilities/Table.vue";
  //Composables
  import useTFacturas from "./../../composables/useTFacturas";
  export default {
    setup() {
      const { items, sortBy, headers, itemsSelect, initialSearchField } =
        useTFacturas();
  
      const personalValue = document
        .querySelector("[data-tfacturas]")
        .getAttribute("data-tfacturas");
      items.value = JSON.parse(personalValue);
  
      //Composables
      return {
        items,
        sortBy,
        headers,
        itemsSelect,
        initialSearchField,
      };
    },
    components: { Table },
  };
  </script>
  
  <style></style>
  