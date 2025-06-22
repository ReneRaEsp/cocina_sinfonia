<template>
  <v-content class="content">
    <v-container class="elevation-2 mb-4 rounded" fluid>
      <v-row>
        <v-col class="d-flex justify-content-between" cols="12">
          <div cols="3">
            <v-btn @click="vaciarPapelera" color="red-accent-3">
              Vaciar papelera
            </v-btn>
          </div>
          <div cols="6"></div>
          <div cols="3">
            <v-btn @click="listAllPapelera" color="blue-darken-1 text-light">
              Refrescar papelera
            </v-btn>
          </div>
        </v-col>
      </v-row>
      <v-row>
        <v-col
          @mouseenter="onMousemove"
          @mouseleave="onMousemove"
          style="height: 800px"
          cols="12"
          class="bg-light"
        >
          <v-card
            class="d-flex justify-content-between pa-4 ma-2 col-12"
            v-for="item in papelera"
            :key="item.id"
          >
            <div class="col-2">
              Mesa: {{ item.numero }} - {{ item.localizacion }}
              <br />
              Comensales: {{ item.comensales }}
            </div>
            <div class="col-7">
              <v-chip v-for="pedido of item.pedidos" :key="pedido?.id">
                {{ pedido?.pedido }}
              </v-chip>
            </div>
            <div class="d-flex col-3 justify-content-end">
              <v-btn
                @click="restoreItem(item)"
                class="d-flex justify-content-end"
                color="blue-darken-1"
              >
                Restaurar
              </v-btn>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </v-content>
</template>
<script>
import { ref } from "vue";
export default {
  setup() {
    const papelera = ref([]);
    const mouseMoved = ref(false);

    const listAllPapelera = async () => {
      const URL = `/listpapelera`;
      const OPTIONS = {
        method: "GET",
      };
      try {
        let res = await fetch(URL, OPTIONS);
        res = await res.json();
        papelera.value = res.papelera;
      } catch (e) {
        console.log(e);
      }
    };

    const restoreItem = async (item) => {
      const URL = `/restaurarmesa`;
      const OPTIONS = {
        method: "POST",
        body: JSON.stringify({
          id: item.id,
        }),
      };
      try {
        let res = await fetch(URL, OPTIONS);
        res = await res.json();
      } catch (e) {
        console.log(e);
      }
      listAllPapelera();
    };

    const vaciarPapelera = async () => {
      const URL = `/vaciarpapelera`;
      const OPTIONS = {
        method: "DELETE",
      };
      try {
        let res = await fetch(URL, OPTIONS);
        res = await res.json();
      } catch (e) {
        console.log(e);
      }
      listAllPapelera();
    };

    const onMousemove = () => {
      listAllPapelera();
    };

    listAllPapelera();
    return {
      papelera,
      mouseMoved,
      listAllPapelera,
      onMousemove,
      restoreItem,
      vaciarPapelera,
    };
  },
};
</script>
<style lang="scss" scoped>
.content {
  width: 100%;
}
</style>
