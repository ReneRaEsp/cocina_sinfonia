import { reactive } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { numeric, required, helpers } from "@vuelidate/validators";

const useTienda = (props) => {
  const initialState = {
    id: 0,
    name: "",
    pvp: 0,
  };

  const state = reactive({
    ...initialState,
  });

  const rules = {
    name: { required: helpers.withMessage("Campo requerido", required) },
    pvp: {
      required: helpers.withMessage("Campo requerido", required),
      numeric: helpers.withMessage("Solo admite numeros", required),
    },
  };

  console.log("props.editData:", props.editData);

  // state.selectProveedores = new Set(props.selectProveedores.map((i) => i.p_id));
  // state.selectProveedores = [...state.selectProveedores];
  // const items = Array.from(state.selectProveedores);
  if (props.editData) {
    state.id = props.editData.id;
    state.name = props.editData.name;
    state.pvp = props.editData.pvp;
  }

  const v$ = useVuelidate(rules, state);

  const updateTienda = async () => {
    try {
      const URL = `/updatetienda`;
      const OPTIONS = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          id: state.id,
          name: state.name,
          pvp: state.pvp,
        }),
      };
      //console.log(`Editar`);
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
        await updateTienda();
      }
      window.location.href = "/productostienda";
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
    // items,
    v$,
  };
};

export default useTienda;
