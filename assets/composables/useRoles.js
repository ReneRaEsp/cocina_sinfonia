import { ref } from "vue";

const useRoles = () => {
  const rolesItems = ref([]);
  const rolesSortBy = ["username", "roles"];

  const rolesHeaders = [
    { text: "Nombre de usuario", value: "username", sortable: true },
    { text: "Roles", value: "roles", sortable: true },
    { text: "", value: "eliminar", sortable: false },
  ];

  const rolesItemsSelect = ref([
    { name: "Buscar usuario", value: "username" },
    { name: "Buscar roles", value: "roles" },
    { name: "", value: "" },
  ]);

  const rolesInitialSearchField = ref("username");

  return {
    rolesItems,
    rolesSortBy,
    rolesHeaders,
    rolesItemsSelect,
    rolesInitialSearchField,
  };
};

export default useRoles;
