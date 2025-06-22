<template>
  <v-content>
    <v-container class="elevation-2 mb-4 rounded" fluid>
      <v-row>
        <v-col cols="12" md="12" class="bg-light"> </v-col>
      </v-row>
      <v-row>
        <v-col style="height: 800px" cols="12" class="bg-light">
          <Table
            style="min-height: 400px; height: 400px"
            :headers="headers"
            :items="items"
            :itemsSelect="itemsSelect"
            :initialSearchField="initialSearchField"
            :sortBy="sortBy"
            :archiveName="`proveedores`"
            :titles="modalTitles"
          />
        </v-col>
      </v-row>
    </v-container>
  </v-content>
</template>

<script>
import Table from "./../utilities/Table.vue";
import { ref } from "vue";
export default {
  setup() {
    const items = ref([]);
    const modalTitles = ref({
      addTitle: "Añadir proveedor al stock",
      editTitle: "Editar proveedor",
    });

    const sortBy = ["dir", "email", "id", "name", "nif", "tel"];

    const headers = [
      { text: "ID", value: "id", sortable: true },
      { text: "Nombre", value: "name", sortable: true },
      { text: "Dirección", value: "dir", sortable: true },
      { text: "Email", value: "email", sortable: true },
      { text: "Cif/Nif", value: "nif", sortable: true },
      { text: "Teléfono", value: "telf", sortable: true },
      { text: "Editar/Eliminar", value: "operation" },
    ];
    const itemsSelect = ref([
      { name: "Buscar Id", value: "id" },
      { name: "Buscar Nombre", value: "name" },
      { name: "Buscar Dirección", value: "dir" },
      { name: "Buscar Email", value: "email" },
      { name: "Buscar Cif/Nif", value: "nif" },
      { name: "Buscar Teléfono", value: "telf" },
    ]);
    const initialSearchField = ref("name");

    const proveedoresValue = document
      .querySelector("[data-proveedores]")
      .getAttribute("data-proveedores");
    items.value = JSON.parse(proveedoresValue);

    return {
      items,
      sortBy,
      headers,
      modalTitles,
      itemsSelect,
      initialSearchField,
    };
  },
  components: { Table },
};
</script>

<style></style>
