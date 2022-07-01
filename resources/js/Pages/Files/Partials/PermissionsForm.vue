<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="filename" value="Current File Permissions" />
                <a
                    :href="file.url"
                    :download="file.filename"
                    target="_blank"
                    class="link link-hover"
                    >{{ file.filename }}</a
                >
                <jet-input-error :message="form.errors.file" class="mt-2" />
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
            <input id="client_id" type="hidden" v-model="form.client_id" />
        </template>

        <template #actions>
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { usePage } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { Inertia } from "@inertiajs/inertia";

export default {
    components: {
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: ["clientId", "file"],
    emits: ["success"],
    setup(props, { emit }) {
        let urlPrev = usePage().props.value.urlPrev;
        const form = useGymRevForm({
            permissions: props?.file?.permissions || [],
        });

        let handleSubmit = async () => {
            form.dirty().put(route("files.update", props.file.id));
            emit("success");
        };

        return {
            form,
            buttonText: "Update",
            handleSubmit,
            urlPrev,
        };
    },
};
</script>

<style scoped>
.form-control {
    @apply col-span-6 flex flex-row gap-4;
}
</style>
