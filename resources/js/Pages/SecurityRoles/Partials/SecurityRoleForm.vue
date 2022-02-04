<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="name" value="Name" />
                <input
                    id="name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.security_role"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors.security_role"
                    class="mt-2"
                />
            </div>

            <div class="col-span-6">
                <jet-label for="role" value="Role" />
                <select
                    id="role"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.role"
                    autofocus
                >
                    <option v-for="availableRole in availableRoles">
                        {{ availableRole.name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.security_role"
                    class="mt-2"
                />
            </div>

            <div
                class="col-span-6"
                v-for="([groupName, availableAbilities]) in Object.entries(
                    groupedAvailableAbilities
                )"
            >
                {{groupName}}
                <template v-for="availableAbility in availableAbilities">
                    <jet-label for="abilities" :value="availableAbility.name" />
                    <input
                        id="abilities"
                        type="checkbox"
                        class="block w-full mt-1"
                        v-model="form.security_role"
                        autofocus
                /></template>
                <jet-input-error
                    :message="form.errors.security_role"
                    class="mt-2"
                />
            </div>
            <!--            <input id="client_id" type="hidden" v-model="form.client_id" />-->
        </template>

        <template #actions>
            <Button
                type="button"
                @click="$inertia.visit(route('security-roles'))"
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
import { computed } from "vue";
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
        availableRoles: {
            type: Array,
            default: [],
        },
        availableAbilities: {
            type: Array,
            default: [],
        },
        securityRole: {
            type: Object,
        },
    },
    setup(props, context) {
        let securityRole = props.securityRole;
        let operation = "Update";
        if (!securityRole) {
            securityRole = {
                security_role: null,
                client_id: props.clientId,
            };
            operation = "Create";
        }

        const form = useForm(securityRole);

        let handleSubmit = () =>
            form.put(route("security-roles.update", securityRole.id));
        if (operation === "Create") {
            handleSubmit = () => form.post(route("security-roles.store"));
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
            console.log({ grouped });
            return grouped;
        });

        return {
            form,
            buttonText: operation,
            handleSubmit,
            groupedAvailableAbilities,
        };
    },
};
</script>
