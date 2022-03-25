<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="title" value="Title" />
                <input
                    id="title"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.title"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors.title"
                    class="mt-2"
                />
            </div>

        </template>

        <template #actions>
            <Button
                type="button"
                @click="$inertia.visit(route('classifications'))"
                :class="{ 'opacity-25': form.processing }"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </Button>
            <div class="flex-grow" />
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
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";

import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
    },
    props: {
        clientId: {
            type: String,
            required: true,
        },
        availableAbilities: {
            type: Array,
            default: [],
        },
        classification: {
            type: Object,
        },
    },
    setup(props, context) {
        let classification = props.classification;
        let operation = "Update";
        if (!classification) {
            classification = {
                title: null,
                id: null,
                client_id: props.clientId,
            };
            operation = "Create";
        }

        const form = useForm(classification);

        let handleSubmit = () =>
            form.put(route("classifications.update", securityRole.id));
        if (operation === "Create") {
            handleSubmit = () => form.post(route("classifications.store"));
        }

        let groupedAvailableAbilities = computed(() => {
            let grouped = {};
            props.availableAbilities.forEach((availableAbility) => {
                let group = availableAbility.name.split(".")[0];
                if (grouped[group]) {
                    grouped[group] = [...grouped[group], availableAbility];
                } else {
                    grouped[group] = [availableAbility];
                }
            });
            return grouped;
        });

        const selectAll = (group) => {
            const groupAbilities = groupedAvailableAbilities.value[group].map(
                (group) => group.id
            );
            const merged = new Set([...form.ability_ids, ...groupAbilities]);
            form.ability_ids = [...merged];
        };

        const clear = (group) => {
            const groupAbilities = groupedAvailableAbilities.value[group].map(
                (group) => group.id
            );
            const merged = form.ability_ids.filter((abilityId) =>
                !groupAbilities.includes(abilityId)
            );
            form.ability_ids = [...merged];
        };

        return {
            form,
            buttonText: operation,
            handleSubmit,
            groupedAvailableAbilities,
            selectAll,
            clear,
        };
    },
};
</script>
