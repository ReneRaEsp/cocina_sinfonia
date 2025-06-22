import { ref } from "vue";

const useTFacturas = () => {
  const items = ref([]);

  const sortBy = ["nombre", "fecha", "empresa"];

  const headers = [
    { text: "ID", value: "id", sortable: true },
    { text: "ref", value: "ticketref", sortable: true },
    { text: "Fecha", value: "fecha", sortable: true },
    { text: "", value: "ruta", sortable: false },
  ];
  const itemsSelect = ref([
    { name: "Buscar Ref", value: "ref" },
    { name: "Buscar Fecha", value: "fecha" },
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

export default useTFacturas;
