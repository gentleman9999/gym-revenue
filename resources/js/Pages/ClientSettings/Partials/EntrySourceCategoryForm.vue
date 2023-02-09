<template>
    <jet-form-section @submitted="handleSubmit" collapsable>
        <template #title>Entry Source Categories</template>

        <template #description>Clients Entry Source Categories</template>

        <template #form>
            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
                v-for="entrySourceCategory in entrySourceCategories"
            >
                <jet-label for="entrySourceCategory.name" value="Name" />
                <input
                    :id="entrySourceCategory.name"
                    @change="escUpdate"
                    :disabled="form.isDirty && form.isAddEsc"
                    type="text"
                    v-model="form.entrySourceCategories[entrySourceCategory.id]"
                />
                <button
                    type="button"
                    @click="handleClickDelete(entrySourceCategory.id)"
                >
                    <recycle-bin :icon-size="iconSize" />
                </button>
                <jet-input-error
                    :message="form.errors.entrySourceCategories"
                    class="mt-2"
                />
            </div>
            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
            >
                <jet-label for="name" value="Name" />
                <input
                    id="name"
                    type="text"
                    :disabled="form.isDirty && form.isUpdateEsc"
                    @change="escAdd"
                    v-model="form.name"
                />
            </div>
        </template>
        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="
                    form.processing || !form.isDirty || !form.isUpdateEsc
                "
            >
                Save
            </Button>
            <button
                type="button"
                @click="resolvedHandleAdd"
                :class="{ 'opacity-25': form.processing }"
                class="btn btn-primary"
                :disabled="form.processing || !form.isDirty || !form.isAddEsc"
                outline
            >
                Add
            </button>
        </template>
    </jet-form-section>
</template>

<script>
import { computed, defineComponent, ref, watchEffect } from "vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useGymRevForm } from "@/utils";
import RecycleBin from "@/Components/RecycleBinItem/RecycleBin.vue";
import { Inertia } from "@inertiajs/inertia";

export default defineComponent({
    components: {
        RecycleBin,
        JetActionMessage,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {
        entrySourceCategories: {
            type: Array,
            required: true,
        },

        // handleAdd: { type: Function },
    },

    setup(props) {
        console.log({ entrySourceCategories: props.entrySourceCategories });
        const iconSize = computed({
            get() {
                return props.mode === "desktop" ? "3x" : "2x";
            },
        });
        const map = {};
        let isAddEsc = false;
        let isUpdateEsc = false;
        props.entrySourceCategories.map(({ id, name }) => (map[id] = name));
        console.log({ map });
        const form = useGymRevForm({
            entrySourceCategories: map,
            name: props.name,
            isAddEsc: isAddEsc,
            isUpdateEsc: isUpdateEsc,
        });
        const confirmDelete = ref(null);
        const handleClickDelete = (item) => {
            console.log("click delete", item);
            confirmDelete.value = item;
            handleConfirmDelete(confirmDelete.value);
        };
        const handleConfirmDelete = () => {
            Inertia.delete(
                route("entry-source-categories.trash", confirmDelete.value)
            );
            confirmDelete.value = null;
        };
        let resolvedHandleAdd = () => {
            console.log(form.data);
            form.post(route("entry-source-categories.store"), {
                preserveState: false,
            });
        };
        let escAdd = () => {
            form.isAddEsc = true;
        };

        let escUpdate = () => {
            form.isUpdateEsc = true;
        };
        let handleSubmit = () =>
            form
                .dirty()
                .put(route("settings.client-entry-source-categories.update"), {
                    preserveState: false,
                });

        return {
            form,
            handleSubmit,
            handleClickDelete,
            handleConfirmDelete,
            resolvedHandleAdd,
            escUpdate,
            escAdd,
        };
    },
});
</script>
