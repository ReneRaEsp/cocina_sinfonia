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
import useFacturas from "./../../composables/useFacturas";
export default {
  setup() {
    const { items, sortBy, headers, itemsSelect, initialSearchField } =
      useFacturas();

    const personalValue = document
      .querySelector("[data-facturas]")
      .getAttribute("data-facturas");
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
