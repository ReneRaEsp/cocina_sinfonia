<template>
  <form class="bg-light">
    <v-text-field
      v-model="state.name"
      :error-messages="v$.name.$errors.map((e) => e.$message)"
      :counter="10"
      label="Nombre"
      required
      @input="v$.name.$touch"
      @blur="v$.name.$touch"
    ></v-text-field>
    <v-text-field
      v-model="state.description"
      :error-messages="v$.description.$errors.map((e) => e.$message)"
      label="Descripción"
      @input="v$.description.$touch"
      @blur="v$.description.$touch"
    ></v-text-field>
    <v-select
      v-model="state.provider"
      :items="items"
      :error-messages="v$.provider.$errors.map((e) => e.$message)"
      label="Proveedor"
      required
      @change="v$.provider.$touch"
      @blur="v$.provider.$touch"
    ></v-select>
    <v-text-field
      v-model="state.amount"
      :error-messages="v$.amount.$errors.map((e) => e.$message)"
      label="Monto"
      @input="v$.amount.$touch"
      @blur="v$.amount.$touch"
      type="number"
    ></v-text-field>
    <v-btn color="light-blue-darken-4" class="me-4" @click="submitForm(v$)">
      Guardar
    </v-btn>
    <!--<v-btn @click="clear(v$)"> clear </v-btn>-->
  </form>
</template>

<script setup>
import useStock from "./../../../composables/useStock";

console.log('entro')

const props = defineProps({
  editData: {
    type: Object,
    required: false,
  },
  isEdit: {
    type: Boolean,
    default: false,
  },
  selectProveedores: {
    type: Array,
  },
  isActive: {
    type: Boolean,
  },
});

const { state, items, v$, submitForm, clear } = useStock(props);
</script>
