import { ref } from "vue";

const useFacturas = () => {
  const items = ref([]);

  const sortBy = ["nombre", "fecha", "empresa"];

  const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "Nombre", value: "nombre", sortable: true },
    { text: "Concepto", value: "concepto", sortable: true },
    { text: "Fecha", value: "fecha", sortable: true },
    { text: "Empresa", value: "empresa", sortable: true },
    { text: "Tipo", value: "tipo", sortable: true },
    { text: "Importe", value: "importe", sortable: true },
    { text: "", value: "ruta", sortable: false },
  ];
  const itemsSelect = ref([
    { name: "Buscar Nombre", value: "nombre" },
    { name: "Buscar Fecha", value: "fecha" },
    { name: "Buscar Tipo", value: "tipo" },
    { name: "Buscar Concepto", value: "concepto" },
    { name: "Buscar Empresa", value: "empresa" },
  ]);
  const initialSearchField = ref("nombre");

  return {
    items,
    sortBy,
    headers,
    itemsSelect,
    initialSearchField,
  };
};

export default useFacturas;
