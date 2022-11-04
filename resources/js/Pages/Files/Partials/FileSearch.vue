<template>
    <div class="flex flex-row w-full justify-end mt-2">
        <simple-search-filter
            @updateSearch="$emit('search', value)"
            class="w-full max-w-md mr-4 col-span-3 lg:col-span-1"
        >
            <div class="block py-2 text-xs text-base-content text-opacity-80">
                Trashed:
            </div>
            <select v-model="form.trashed" class="mt-1 w-full form-select">
                <option :value="null" />
                <option value="with">With Trashed</option>
                <option value="only">Only Trashed</option>
            </select>
        </simple-search-filter>
    </div>
</template>
<script setup>
import SimpleSearchFilter from "@/Components/CRUD/SimpleSearchFilter.vue";

const props = defineProps({
    form: Object,
});
</script>
