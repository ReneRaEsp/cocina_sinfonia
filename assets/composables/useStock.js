import { reactive } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { numeric, required, helpers } from "@vuelidate/validators";

const useStock = (props) => {

  const initialState = {
    id: 0,
    name: "",
    description: "",
    amount: 0,
    provider: "",
    typeFood: "",
    selectProveedores: [],
  };

  const state = reactive({
    ...initialState,
  });

  const rules = {
    name: { required: helpers.withMessage("Campo requerido", required) },
    description: {},
    amount: {
      required: helpers.withMessage("Campo requerido", required),
      numeric: helpers.withMessage("Solo admite numeros", required),
    },
    provider: { required: helpers.withMessage("Campo Requerido", required) },
  };

  state.selectProveedores = new Set(props.selectProveedores.map((i) => i.p_id));
  state.selectProveedores = [...state.selectProveedores];
  const items = Array.from(state.selectProveedores);
  if (props.editData) {
    state.name = props.editData.name;
    state.description = props.editData.tf_id;
    state.provider = props.editData.p_id;
    state.amount = props.editData.amount;
    state.id = props.editData.id;
  }

  const v$ = useVuelidate(rules, state);

  const updateStock = async () => {
    try {
      const URL = `/updatestock`;
      const OPTIONS = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          id: state.id,
          name: state.name,
          description: state.description,
          provider: state.provider,
          amount: state.amount,
        }),
      };
      //console.log(`Editar`);
      let res = await fetch(URL, OPTIONS);
      res = await res.json();
    } catch (e) {
      console.log(e);
    }
  };

  const addStock = async () => {
    try {
      const URL = `/addstock`;
      const OPTIONS = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name: state.name,
          description: state.description,
          provider: state.provider,
          amount: state.amount,
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
        await updateStock();
      } else {
        await addStock();
      }
      window.location.href = "/stock";
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
    items,
    v$,
  };
};

export default useStock;
