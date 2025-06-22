<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-btn
          v-if="isDownloadable"
          @click="generatePdf"
          color="light-blue-darken-4 ma-2"
        >
          Descargar PDF
        </v-btn>
        <v-btn
          v-if="isDownloadable"
          @click="exportToCsv"
          color="light-blue-darken-4 ma-2"
        >
          Descargar CSV
        </v-btn>
        <v-btn
          v-if="isDownloadable"
          @click="exportToExcel"
          color="light-blue-darken-4 ma-2"
        >
          Descargar Excel
        </v-btn>
      </v-col>
    </v-row>
    <v-select
      :items="itemsSelect"
      item-title="name"
      item-value="value"
      v-model="searchField"
      density="compact"
      variant="underlined"
      class="bg-light pa-2"
    ></v-select>
    <v-text-field
      v-model="searchValue"
      :label="`Buscar`"
      class="pa-2 bg-light"
      type="text"
    ></v-text-field>
    <EasyDataTable
      :headers="headers"
      :items="items"
      :search-field="searchField"
      :search-value="searchValue"
      theme-color="#1d90ff"
      table-class-name="customize-table"
      header-text-direction="center"
      body-text-direction="center"
      buttons-pagination
      border-cell
      multi-sort
      ref="table"
      :header-item-class-name="headerItemClassNameFunction"
      :body-item-class-name="bodyItemClassNameFunction"
      style="border: 0px solid #050505"
    >
      <template class="bg-light" v-if="titles?.addTitle" #item-operation="item">
        <v-dialog
          class="bg-light"
          transition="dialog-top-transition"
          width="auto"
        >
          <template v-slot:activator="{ props }">
            <v-btn color="blue-grey-lighten-5" v-bind="props">
              <v-icon
                color="blue-grey-darken-2"
                icon="mdi-text-box-edit"
              ></v-icon>
            </v-btn>
          </template>
          <template class="bg-light" v-slot:default="{ isActive }">
            <v-card color="light">
              <v-toolbar color="light" :title="titles.editTitle"> </v-toolbar>
              <v-card-text style="width: 500px">
                <StockForm
                  v-if="archiveName === 'stock'"
                  :editData="item"
                  :selectProveedores="items"
                  :isActive="isActive"
                />
                <ProveedoresForm
                  v-else-if="archiveName === 'proveedores'"
                  :editData="item"
                  :isEdit="true"
                  :isActive="isActive"
                />
                <TiendaForm
                  v-else-if="archiveName === 'tienda'"
                  :editData="item"
                  :isEdit="true"
                  :isActive="isActive"
                />
              </v-card-text>
              <v-card-actions class="justify-end">
                <v-btn variant="text" @click="isActive.value = false"
                  >Cerrar</v-btn
                >
              </v-card-actions>
            </v-card>
          </template>
        </v-dialog>
        <!-- <v-icon @click="deleteItem(item.id)" icon="mdi-delete"></v-icon> -->
      </template>
      <template class="bg-light" #item-ruta="item">
        <!-- <v-dialog
          max-width="none"
          persistent
          class="bg-light"
          transition="dialog-top-transition"
        >
          <template v-slot:activator="{ props }">
            <v-btn
              @click="scrapePdf(item.ruta)"
              density="comfortable"
              v-bind="props"
              color="light-blue-darken-4"
              icon="mdi-magnify-expand"
            >
            </v-btn>
          </template>
          <template class="bg-light" v-slot:default="{ isActive }">
            <v-card class="d-flex justify-content-center" color="light">
              <v-toolbar color="light" :title="`Visualizar factura`">
                <v-btn
                  icon="mdi-close"
                  variant="text"
                  @click="isActive.value = false"
                ></v-btn>
              </v-toolbar>
              <div style="width: 50%; height: 1000px">
                <v-card>
                  <v-list>
                    <v-list-item
                      v-if="pdfData?.DNI"
                      title="DNI"
                      :subtitle="pdfData?.DNI"
                    ></v-list-item>
                    <v-list-item
                      v-if="pdfData?.NIF"
                      title="NIF"
                      :subtitle="pdfData?.NIF"
                    ></v-list-item>
                    <v-list-item
                      v-if="pdfData?.nifCif"
                      title="NIF/CIF"
                      :subtitle="pdfData?.nifCif"
                    ></v-list-item>
                    <v-list-item
                      v-if="pdfData?.fecha"
                      title="Fecha"
                      :subtitle="pdfData?.fecha"
                    ></v-list-item>
                    <v-list-item
                      v-if="pdfData?.fechaVenta"
                      title="Fecha de Venta"
                      :subtitle="pdfData?.fechaVenta"
                    ></v-list-item>
                    <v-list-item
                      v-if="pdfData?.fechaFacturacion"
                      title="Fecha de Facturacion"
                      :subtitle="pdfData?.fechaFacturacion"
                    ></v-list-item>
                    <v-list-item
                      v-if="pdfData?.fechaImpresion"
                      title="Fecha de Impresión"
                      :subtitle="pdfData?.fechaImpresion"
                    ></v-list-item>
                    <v-list-item
                      v-if="pdfData?.fechaEtrega"
                      title="Fecha de Entrega"
                      :subtitle="pdfData?.fechaEtrega"
                    ></v-list-item>
                    <v-list-item
                      v-if="pdfData?.baseImponible"
                      title="Base Imponible"
                      :subtitle="`${pdfData?.baseImponible} €`"
                    ></v-list-item>
                    <v-list-item
                      v-if="pdfData?.iva"
                      title="IVA"
                      :subtitle="pdfData?.iva"
                    ></v-list-item>
                    <v-list-item
                      v-if="pdfData?.mercancias"
                      title="Mercancía"
                      :subtitle="pdfData?.mercancias"
                    >
                      <v-list>
                        <v-list-item
                          v-for="(mercancia, index) in pdfData?.mercancias"
                          :key="index"
                        >
                          {{ mercancia?.mercancia }} € -
                          {{ mercancia?.porcentaje }}% -
                          {{ mercancia?.totalMercancia }} €
                        </v-list-item>
                      </v-list>
                    </v-list-item>
                    <v-list-item
                      v-if="pdfData?.porcentajeIva"
                      title="Porcentaje Iva"
                      :subtitle="`${pdfData?.porcentajeIva}%`"
                    >
                    </v-list-item>
                    <v-list-item
                      v-if="pdfData?.total"
                      title="Total"
                      :subtitle="`${pdfData?.total} €`"
                    ></v-list-item>
                  </v-list>
                </v-card>
              </div>
            </v-card>
          </template>
        </v-dialog>-->

        <v-dialog
          max-width="none"
          persistent
          class="bg-light"
          transition="dialog-top-transition"
        >
          <template v-slot:activator="{ props }">
            <v-btn
              density="comfortable"
              v-bind="props"
              color="blue"
              icon="mdi-eye"
            >
            </v-btn>
          </template>
          <template class="bg-light" v-slot:default="{ isActive }">
            <v-card color="light">
              <v-toolbar color="light" :title="`Visualizar factura`">
                <v-btn
                  icon="mdi-close"
                  variant="text"
                  @click="isActive.value = false"
                ></v-btn>
              </v-toolbar>
              <div style="width: 100%; height: 1000px">
                <iframe
                  style="width: 100%; height: 100%; border: none"
                  allowfullscreen
                  :src="item.ruta"
                  frameborder="0"
                ></iframe>
              </div>
            </v-card>
          </template>
        </v-dialog>

        <v-btn
          density="comfortable"
          color="green-darken-2"
          icon="mdi-file-download-outline"
          :href="item.ruta"
          download="factura.pdf"
        >
        </v-btn>
        <v-btn
          @click="deleteFactura(item.id)"
          density="comfortable"
          color="red-darken-2"
          icon="mdi-trash-can"
        >
        </v-btn>
      </template>
      <template class="bg-light" #item-importe="item">
        {{ item?.importe }} €
      </template>
      <template class="d-flex flex-column" #item-roles="item">
        <div class="d-flex flex-row justify-start align-start">
          <div v-for="role in item.roles" :key="role" class="d-flex">
            <v-chip variant="flat" color="light-blue-lighten-4">
              {{ role }}
            </v-chip>
          </div>
          <v-icon
            icon="mdi-file-document-edit"
            color="green"
            size="x-large"
            density="compact"
            @click="setHighlighted(item.id, item)"
          ></v-icon>
          <div class="d-flex highlighted" v-if="highlighted === item.id">
            <v-select
              class="select-roles"
              v-model="rolesValue"
              :items="rolesItems"
              chips
              density="compact"
              label=""
              multiple
              variant="outlined"
            ></v-select>
            <v-icon
              icon="mdi-content-save-check"
              color="blue-grey-darken-1"
              size="x-large"
              density="compact"
              @click="onSaveNewRole(item)"
            ></v-icon>
            <v-icon
              icon="mdi-close-box"
              color="red-darken-1"
              size="x-large"
              density="compact"
              @click="setHighlighted(item.id, item)"
            ></v-icon>
          </div>
        </div>
      </template>
      <template class="bg-light" #item-eliminar="item">
        <v-icon
          icon="mdi-trash-can"
          color="red-darken-1"
          size="x-large"
          density="compact"
          @click="onDeleteUser(item)"
        ></v-icon>
      </template>
      <template class="bg-light" #item-total="item">
        {{ item.total.toFixed(2).toString().replace(".", ",") }} €
      </template>
      <template class="bg-light" #item-cantidad="item">
        {{ item.cantidad.toFixed(2).toString().replace(".", ",") }} €
      </template>
      <template class="bg-light" #item-total_efectivo="item">
        <span v-if="item.total_efectivo > 0">
          {{ item.total_efectivo.toFixed(2).toString().replace(".", ",") }} €
        </span>
      </template>
      <template class="bg-light" #item-total_tarjeta="item">
        <span v-if="item.total_tarjeta > 0">
          {{ item.total_tarjeta.toFixed(2).toString().replace(".", ",") }} €
        </span>
      </template>
      <template class="bg-light" #item-ref="item">
        <v-dialog max-width="500">
          <template v-slot:activator="{ props: activatorProps }">
            <v-btn
              class="elevation-2"
              v-bind="activatorProps"
              color="bg-light"
              text="Open Dialog"
              icon="mdi-magnify"
              variant="flat"
            ></v-btn>
          </template>
          <template v-slot:default="{ isActive }">
            <v-card title="">
              <v-card-text>
                <VentasModal :pedidos="item.pedidos_ref" />
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn text="Cerrar" @click="isActive.value = false"></v-btn>
              </v-card-actions>
            </v-card>
          </template>
        </v-dialog>
      </template>
    </EasyDataTable>
  </v-container>
</template>

<script>
import { ref, defineComponent, onMounted } from "vue";
//Components
import StockForm from "./stock/StockForm.vue";
import ProveedoresForm from "./proveedores/ProveedoresForm.vue";
import TiendaForm from "./tienda/TiendaForm.vue";
import VentasModal from "./../utilities/ventas/Modal.vue";
//Composables
import useDownloads from "./../../composables/useDownloads";
import usePdf from "./../../composables/usePdf";

export default defineComponent({
  setup(props) {
    const itemsSelect = props.itemsSelect;
    const items = ref([]);
    const modalTitle = props?.modalTitle;
    const highlighted = ref(0);
    const rolesItems = ref([
      "ROLE_ADMIN",
      "ROLE_CAMARERO",
      "ROLE_GESTOR",
      "ROLE_USER",
    ]);
    const rolesValue = ref([]);

    const sortBy = props.sortBy;
    const sortType = ["desc", "asc"];

    const searchField = ref("");
    searchField.value = props.initialSearchField;
    const searchValue = ref("");

    const headers = props.headers;
    items.value = props.items;

    const editIem = async (item) => {
      alert(item);
    };

    const { tableRef, archiveName, generatePdf, exportToCsv, exportToExcel } =
      useDownloads(props);
    const { pdfData, scrapePdf } = usePdf();

    const setHighlighted = (id, item) => {
      highlighted.value === id
        ? (highlighted.value = 0)
        : (highlighted.value = id);
      rolesValue.value = item.roles;
    };

    const onSaveNewRole = async (item) => {
      const OPTIONS = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          email: item.email,
          value: rolesValue.value,
        }),
      };
      const URL = "/setrole";
      try {
        let res = await fetch(URL, OPTIONS);
        res = await res.json();
        window.location.href = "/ajustes";
      } catch (e) {
        console.log(e);
      }
    };

    const onDeleteUser = async (item) => {
      const OPTIONS = {
        headers: { "Content-Type": "application/json" },
        method: "DELETE",
        body: JSON.stringify({
          id: item.id,
        }),
      };
      const URL = "/deleteuser";
      try {
        let res = await fetch(URL, OPTIONS);
        res = await res.json();
        window.location.href = "/ajustes";
      } catch (e) {
        console.log(e);
      }
    };

    const deleteItem = async (itemId) => {
      const URL = `/proveedores_list?itemId=${itemId}`;
      try {
        let res = await fetch(URL);
        res = await res.json();
      } catch (e) {
        console.log(e);
      }
    };

    const deleteFactura = async (itemId) => {
      const URL = `/deletefacturas?id=${itemId}`;
      try {
        let res = await fetch(URL);
        res = await res.json();
        Swal.fire({
          title: "Eliminación correcta",
          text: res.factura,
          icon: "success",
          confirmButtonText: "OK",
        });

        const index = items.value.findIndex((item) => item.id === itemId);
        if (index !== -1) {
          items.value.splice(index, 1);
        }
      } catch (e) {
        console.log(e);
        location.reload();
      }
    };

    const onShowRef = async (item) => {
      /*const URL = `/deletefacturas?id=${itemId}`;
      try {
        let res = await fetch(URL);
        res = await res.json();
        Swal.fire({
          title: "Eliminación correcta",
          text: res.factura,
          icon: "success",
          confirmButtonText: "OK",
        });
        console.log(res);

        const index = items.value.findIndex((item) => item.id === itemId);
        if (index !== -1) {
          items.value.splice(index, 1);
        }
      } catch (e) {
        console.log(e);
      }*/
      console.log(item);
    };

    const bodyItemClassNameFunction = (column) => {
      if (column === "operation") return "operation-column";
      return "";
    };

    const headerItemClassNameFunction = (header) => {
      if (header.value === "operation") return "operation-column";
      return "";
    };

    onMounted(() => {
      tableRef.value = document.querySelector("table");
    });

    return {
      headers,
      items,
      pdfData,
      highlighted,
      rolesItems,
      rolesValue,
      itemsSelect,
      sortBy,
      sortType,
      tableRef,
      searchField,
      searchValue,
      scrapePdf,
      modalTitle,
      archiveName,
      editIem,
      deleteItem,
      generatePdf,
      exportToCsv,
      exportToExcel,
      bodyItemClassNameFunction,
      headerItemClassNameFunction,
      setHighlighted,
      onSaveNewRole,
      onDeleteUser,
      deleteFactura,
      onShowRef,
    };
  },
  name: "Stock",
  props: {
    headers: {
      type: Array,
    },
    sortBy: {
      type: Array,
    },
    items: {
      type: Array,
    },
    itemsSelect: {
      type: Array,
    },
    initialSearchField: {
      type: String,
    },
    archiveName: {
      type: String,
    },
    modalTitle: {
      type: String,
    },
    addTitle: {
      type: String,
    },
    titles: {
      type: Object,
      required: false,
    },
    isDownloadable: {
      type: Boolean,
      required: false,
      default: true,
    },
    hasEdit: {
      type: Boolean,
      required: false,
      default: true,
    },
  },
  components: {
    StockForm,
    ProveedoresForm,
    VentasModal,
    TiendaForm,
  },
});
</script>

<style lang="scss" scope>
.customize-table {
  --easy-table-border: 1px solid #fff;
  --easy-table-row-border: 1px solid #878787;

  --easy-table-header-font-size: 14px;
  --easy-table-header-height: 50px;
  --easy-table-header-font-color: rgba(20, 29, 29, 1);
  --easy-table-header-background-color: #fff;
  --easy-table-header-item-padding: 10px 15px;

  --easy-table-body-even-row-font-color: #000;
  --easy-table-body-even-row-background-color: #fff;
  --easy-table-body-row-font-color: rgba(20, 29, 29, 1);
  --easy-table-body-row-background-color: #fff;
  --easy-table-body-row-height: 20px;
  --easy-table-body-row-font-size: 12px;

  --easy-table-body-row-hover-font-color: #2d3a4f;
  --easy-table-body-row-hover-background-color: #eee;

  --easy-table-body-item-padding: 10px 15px;

  /*--easy-table-footer-background-color: #2d3a4f;*/
  --easy-table-footer-background-color: #fff;
  --easy-table-footer-font-color: #000;
  --easy-table-footer-font-size: 14px;
  --easy-table-footer-padding: 0px 10px;
  --easy-table-footer-height: 50px;

  --easy-table-rows-per-page-selector-width: 70px;
  --easy-table-rows-per-page-selector-option-padding: 10px;
  --easy-table-rows-per-page-selector-z-index: 1;

  --easy-table-scrollbar-track-color: rgba(190, 233, 233, 0.9);
  --easy-table-scrollbar-color: rgba(190, 233, 233, 0.9);
  --easy-table-scrollbar-thumb-color: #4c5d7a;
  --easy-table-scrollbar-corner-color: #2d3a4f;

  --easy-table-loading-mask-background-color: #2d3a4f;
}
.selected {
  width: 50% !important;
  border-radius: 1000px;
}
.highlighted {
  position: relative;
  .select-roles {
    /*width: 100%;*/
  }
}

.vue3-easy-data-table__main {
  height: 400px;
  max-height: 400px;
}
.button {
  padding: 0 !important;
}
.operation-column {
  font-size: 15px;
}

.v-dialog--fullscreen {
  width: 100vw;
  height: 100vh;
  margin: 0;
}
</style>
