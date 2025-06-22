<template>
  <v-content>
    <v-container class="elevation-2 rounded ma-4" fluid>
      <v-row>
        <v-col cols="10">
          <h5>Control de roles</h5>
        </v-col>
        <v-col style="height: 700px" cols="12" class="bg-light">
          <Table
            style="min-height: 400px; height: 400px"
            :headers="rolesHeaders"
            :items="rolesItems"
            :itemsSelect="rolesItemsSelect"
            :initialSearchField="rolesInitialSearchField"
            :sortBy="rolesSortBy"
            :archiveName="`facturas`"
            :isDownloadable="false"
          />
        </v-col>
      </v-row>
    </v-container>
    <v-container class="elevation-2 rounded ma-4" fluid>
      <v-row>
        <v-col cols="10">
          <h5>Auditoria de roles</h5>
        </v-col>
        <v-col style="height: 700px" cols="12" class="bg-light">
          <Table
            style="min-height: 400px; height: 400px"
            :headers="auditoriaHeaders"
            :items="auditoriaItems"
            :itemsSelect="auditoriaItemsSelect"
            :initialSearchField="auditoriaInitialSearchField"
            :sortBy="auditoriaSortBy"
            :archiveName="`facturas`"
            :isDownloadable="false"
          />
        </v-col>
      </v-row>
    </v-container>
  </v-content>
</template>

<script>
//Composables
import useAjustes from "./../../composables/useAjustes";
import useRoles from "./../../composables/useRoles";
//Components
import Table from "./../utilities/Table.vue";
export default {
  setup() {
    const {
      auditoriaItems,
      auditoriaSortBy,
      auditoriaHeaders,
      auditoriaItemsSelect,
      auditoriaInitialSearchField,
    } = useAjustes();

    const {
      rolesItems,
      rolesSortBy,
      rolesHeaders,
      rolesItemsSelect,
      rolesInitialSearchField,
    } = useRoles();

    const auditoriaslValue = document
      .querySelector("[data-ajustes]")
      .getAttribute("data-ajustes");
    auditoriaItems.value = JSON.parse(auditoriaslValue);

    const roleslValue = document
      .querySelector("[data-users]")
      .getAttribute("data-users");
    rolesItems.value = JSON.parse(roleslValue);

    //Composables
    return {
      auditoriaItems,
      auditoriaSortBy,
      auditoriaHeaders,
      auditoriaItemsSelect,
      auditoriaInitialSearchField,
      rolesItems,
      rolesSortBy,
      rolesHeaders,
      rolesItemsSelect,
      rolesInitialSearchField,
    };
  },
  components: { Table },
};
</script>

<style></style>
