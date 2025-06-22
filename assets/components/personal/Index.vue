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
            :archiveName="`ventas`"
            :isDownloadable="false"
          />
        </v-col>
      </v-row>
    </v-container>
  </v-content>
</template>

<script>
import { ref } from "vue";
//Components
import Table from "./../utilities/Table.vue";
export default {
  setup() {
    const items = ref([]);

    const sortBy = ["usuario", "email", "lastLogin"];

    const headers = [
      { text: "Nombre de Usuario", value: "username", sortable: true },
      { text: "Email", value: "email", sortable: true },
      { text: "Último inicio de sesión", value: "lastLogin", sortable: true },
    ];
    const itemsSelect = ref([
      { name: "Buscar Usuario", value: "username" },
      { name: "Buscar Email", value: "email" },
      { name: "Buscar Último Inicio", value: "lastLogin" },
    ]);
    const initialSearchField = ref("username");

    const personalValue = document
      .querySelector("[data-users]")
      .getAttribute("data-users");
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
