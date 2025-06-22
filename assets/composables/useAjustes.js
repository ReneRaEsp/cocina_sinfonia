import { ref } from "vue";

const useAjustes = () => {
  const auditoriaItems = ref([]);
  const auditoriaSortBy = ["usuario", "modificado", "fecha"];

  const auditoriaHeaders = [
    { text: "Nombre de usuario", value: "usuario", sortable: true },
    { text: "Modificado por", value: "modificado", sortable: true },
    { text: "Fecha modificación", value: "fecha", sortable: true },
    { text: "Roles anteriores", value: "rol_anterior", sortable: false },
    { text: "Roles actuales", value: "rol_nuevo", sortable: false },
  ];

  const auditoriaItemsSelect = ref([
    { name: "Buscar usuario", value: "usuario" },
    { name: "Buscar modificado por", value: "modificado" },
    { name: "Buscar Fecha modificación", value: "fecha" },
    { name: "Buscar roles anteriores", value: "rol_anterior" },
    { name: "Buscar roles nuevos", value: "rol_nuevo" },
  ]);

  const auditoriaInitialSearchField = ref("usuario");

  return {
    auditoriaItems,
    auditoriaSortBy,
    auditoriaHeaders,
    auditoriaItemsSelect,
    auditoriaInitialSearchField,
  };
};

export default useAjustes;
