<template>
    <div class="form-control">
        <label for="client" class="label label-text">Client:</label>
        <select
            id="client"
            @input="$emit('update:modelValue', $event.target.value)"
            :disabled="loading"
            v-bind="$attrs"
        >
            <option v-for="client in clients" :value="client?.id || null">
                {{ client?.name || "Gym Revenue" }}
            </option>
        </select>
    </div>
</template>
<script setup>
import { onMounted, ref } from "vue";

const clients = ref([]);
const loading = ref(false);

const emit = defineEmits("update:modelValue");

onMounted(async () => {
    loading.value = true;
    const response = await axios.get(route("clients"));
    clients.value = [null, ...response.data];
    loading.value = false;
});
</script>
