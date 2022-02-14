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
                    v-model="form.role_id"
                    autofocus
                >
                    <option
                        v-for="availableRole in availableRoles"
                        :value="availableRole.id"
                    >
                        {{ availableRole.name }}
                    </option>
                </select>
                <jet-input-error :message="form.errors.role_id" class="mt-2" />
            </div>

            <div class="col-span-6 uppercase font-bold">Abilities</div>

            <div
                class="col-span-6"
                v-for="[groupName, availableAbilities] in Object.entries(
                    groupedAvailableAbilities
                )"
            >
                <div class="grid grid-cols-6">
                    <div class="flex flex-row col-span-6 items-center pb-4">
                        <span
                            class="font-bold uppercase opacity-50 text-sm mr-4"
                        >
                            {{ groupName }}
                        </span>
                        <!--                        <div class="flex-grow"/>-->
                        <button
                            type="button"
                            class="btn btn-ghost btn-xs opacity-50"
                            @click="selectAll(groupName)"
                        >
                            [Select All]
                        </button>
                        <button
                            type="button"
                            class="btn btn-ghost btn-xs opacity-50"
                            @click="clear(groupName)"
                        >
                            [Clear]
                        </button>
                    </div>

                    <div
                        class="flex flex-row items-center gap-4 col-span-6 xl:col-span-3 pb-4"
                        v-for="availableAbility in availableAbilities"
                    >
                        <input
                            :id="`abilities${availableAbility.title}`"
                            type="checkbox"
                            v-model="form.ability_ids"
                            :value="availableAbility.id"
                            autofocus
                        />
                        <jet-label
                            :for="`abilities${availableAbility.title}`"
                            :value="availableAbility.title"
                        />
                    </div>
                </div>
            </div>
            <jet-input-error :message="form.errors.ability_ids" class="mt-2" />
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
                role_id: null,
                client_id: props.clientId,
                ability_ids: [],
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
