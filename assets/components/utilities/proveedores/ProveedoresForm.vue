<template>
  <form class="proveedores-form bg-light">
    <v-text-field
      v-model="state.name"
      :error-messages="v$.name.$errors.map((e) => e.$message)"
      label="Nombre"
      required
      @input="v$.name.$touch"
      @blur="v$.name.$touch"
    ></v-text-field>
    <v-text-field
      v-model="state.dir"
      :error-messages="v$.dir.$errors.map((e) => e.$message)"
      label="Dirección"
      @input="v$.dir.$touch"
      @blur="v$.dir.$touch"
    ></v-text-field>
    <v-text-field
      v-model="state.email"
      :error-messages="v$.email.$errors.map((e) => e.$message)"
      label="Email"
      @input="v$.email.$touch"
      @blur="v$.email.$touch"
    ></v-text-field>
    <v-text-field
      v-model="state.nif"
      :error-messages="v$.nif.$errors.map((e) => e.$message)"
      label="Cif/Nif"
      @input="v$.nif.$touch"
      @blur="v$.nif.$touch"
    ></v-text-field>
    <v-text-field
      v-model="state.telf"
      :error-messages="v$.telf.$errors.map((e) => e.$message)"
      label="Teléfono"
      @input="v$.telf.$touch"
      @blur="v$.telf.$touch"
    ></v-text-field>
    <v-btn color="light-blue-darken-4" class="me-4" @click="submitForm(v$)">
      Guardar
    </v-btn>
    <!--<v-btn @click="clear(v$)"> clear </v-btn>-->
  </form>
</template>

<script setup>
import useProveedores from "./../../../composables/useProveedores.js";

const props = defineProps({
  editData: {
    type: Object,
    required: false,
  },
  isEdit: {
    type: Boolean,
    default: false,
  },
  isActive: {
    type: Boolean,
  },
});

const { state, items, v$, submitForm, clear } = useProveedores(props);
</script>
