<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="filename" value="Current Permissions" />
                <a
                    :href="item.url"
                    :download="item.filename"
                    target="_blank"
                    class="link link-hover"
                    >{{ itemType == "file" ? item.filename : item.name }}</a
                >
                <jet-input-error :message="form.errors.item" class="mt-2" />
            </div>

            <div class="form-control">
                <input
                    id="regional_admin"
                    value="regional_admin"
                    type="checkbox"
                    v-model="form.permissions"
                />
                <jet-label for="regional_admin" value="Regional Admin" />
            </div>

            <div class="form-control">
                <input
                    id="location_manager"
                    value="location_manager"
                    type="checkbox"
                    v-model="form.permissions"
                />
                <jet-label for="location_manager" value="Location Manager" />
            </div>

            <div class="form-control">
                <input
                    id="employee"
                    value="employee"
                    type="checkbox"
                    v-model="form.permissions"
                />
                <jet-label for="employee" value="Employee" />
            </div>
            <jet-input-error :message="form.errors.permissions" class="mt-2" />
        </template>

        <template #actions>
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                :loading="form.processing"
            >
                Update
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import { defineEmits, computed } from "vue";

import { usePage } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    item: {
        type: Object,
    },
});

const itemType = computed(() => (props.item.filename ? "file" : "folder"));

const emit = defineEmits(["submitted"]);

let urlPrev = usePage().props.value.urlPrev;
const form = useGymRevForm({
    permissions: props?.file?.permissions || [],
});

let handleSubmit = async () => {
    if (itemType.value === "file") {
        form.dirty().put(route("files.update", props.item.id));
        emit("success");
    } else {
        await Inertia.put(route("folders.update", props.item.id), {
            id: props.item.id,
            permissions: form.permissions,
        });
        emit("success");
    }
};
</script>

<style scoped>
.form-control {
    @apply col-span-6 flex flex-row gap-4;
}
</style>
