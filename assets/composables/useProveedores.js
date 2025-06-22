import { reactive } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { numeric, required, helpers } from "@vuelidate/validators";

const useProveedores = (props) => {
  const initialState = {
    id: 0,
    name: "",
    dir: "",
    email: "",
    telf: "",
    nif: "",
  };

  const state = reactive({
    ...initialState,
  });

  const rules = {
    name: { required: helpers.withMessage("Campo requerido", required) },
    dir: {},
    email: {
      email: helpers.withMessage("Solo admite email", required),
    },
    telf: { required: helpers.withMessage("Campo Requerido", required) },
    nif: {
      required: helpers.withMessage("Campo Requerido", required),
      alphanumeric: helpers.withMessage("Solo admite alfanumerico", required),
    },
  };

  if (props.editData) {
    state.id = props.editData.id;
    state.name = props.editData.name;
    state.dir = props.editData.dir;
    state.email = props.editData.email;
    state.telf = props.editData.telf;
    state.nif = props.editData.nif;
  }

  const v$ = useVuelidate(rules, state);

  const updateProveedor = async () => {
    try {
      const URL = `/updateproveedor`;
      const OPTIONS = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          id: state.id,
          name: state.name,
          dir: state.dir,
          email: state.email,
          telf: state.telf,
          nif: state.nif,
        }),
      };
      //console.log(`Editar`);
      let res = await fetch(URL, OPTIONS);
      res = await res.json();
    } catch (e) {
      console.log(e);
    }
  };

  const addProveedor = async () => {
    try {
      const URL = `/addproveedor`;
      const OPTIONS = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name: state.name,
          dir: state.dir,
          email: state.email,
          telf: state.telf,
          nif: state.nif,
        }),
      };
      //console.log(`Añadir`);
      let res = await fetch(URL, OPTIONS);
      res = await res.json();
    } catch (e) {
      console.log(e);
    }
  };

  const submitForm = async () => {
    const isValid = await v$.value.$validate();
    if (isValid) {
      if (state.id) {
        await updateProveedor();
      } else {
        await addProveedor();
      }
      window.location.href = "/proveedores";
    } else {
      console.log("Formulario invalido");
    }
  };

  function clear() {
    v$.value.$reset();

    for (const [key, value] of Object.entries(initialState)) {
      state[key] = value;
    }
  }

  return {
    state,
    submitForm,
    clear,
    v$,
  };
};

export default useProveedores;
