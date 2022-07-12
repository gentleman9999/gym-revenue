<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="title" value="Name" />
                <input
                    id="name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.name"
                    autofocus
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6">
                <jet-label for="group" value="Security Group" />
                <select
                    class="block w-full mt-1"
                    id="group"
                    v-model="form.group"
                >
                    <option
                        v-for="{ name, value } in securityGroups"
                        :value="value"
                    >
                        {{ name }}
                    </option>
                </select>
                <jet-input-error :message="form.errors.group" class="mt-2" />
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
                            :id="`${availableAbility.name}`"
                            type="checkbox"
                            v-model="form.ability_names"
                            :value="availableAbility.name"
                        />
                        <jet-label
                            :for="`abilities${availableAbility.title}`"
                            :value="availableAbility.title"
                        />
                    </div>
                </div>
            </div>
            <jet-input-error
                :message="form.errors.ability_names"
                class="mt-2"
            />
        </template>

        <template #actions>
            <Button
                type="button"
                @click="handleClickCancel"
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
                :disabled="form.processing || !form.isDirty"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { computed, ref } from "vue";
import { useGymRevForm } from "@/utils";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { Inertia } from "@inertiajs/inertia";
import { useModal } from "@/Components/InertiaModal";

export default {
    components: {
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
    },
    props: {
        availableAbilities: {
            type: Array,
            default: [],
        },
        role: {
            type: Object,
        },
        securityGroups: {
            type: Array,
            default: [],
        },
    },
    setup(props, context) {
        let role = props.role;
        let operation = "Update";
        if (!role) {
            role = {
                name: "",
                id: "",
                ability_names: [],
                group: null,
            };
            operation = "Create";
        }

        const form = useGymRevForm({
            name: role.name,
            id: role.id,
            ability_names: getAbilities(),
            group: role.group,
        });

        function getAbilities() {
            if (role.abilities) {
                return role.abilities.map((ability) => ability.name);
            } else {
                return role.ability_names.map((ability) => ability.name);
            }
        }

        let handleSubmit = () =>
            form.dirty().put(route("roles.update", role.id));
        if (operation === "Create") {
            handleSubmit = () => form.post(route("roles.store"));
        }

        let groupedAvailableAbilities = computed(() => {
            let grouped = {};
            props.availableAbilities.forEach((availableAbility) => {
                let group = availableAbility.name.split(".")[0];
                if (group === "*") {
                    return;
                }
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
                (group) => group.name
            );
            const merged = new Set([...form.ability_names, ...groupAbilities]);
            form.ability_names = [...merged];
        };

        const clear = (group) => {
            const groupAbilities = groupedAvailableAbilities.value[group].map(
                (group) => group.name
            );
            const merged = form.ability_names.filter(
                (abilityId) => !groupAbilities.includes(abilityId)
            );
            form.ability_names = [...merged];
        };

        const modal = useModal();

        const handleClickCancel = () => {
            console.log("modal", modal.value);
            if (modal.value.close) {
                modal.value.close();
            } else {
                Inertia.visit(route("roles"));
            }
        };

        return {
            form,
            buttonText: operation,
            handleSubmit,
            groupedAvailableAbilities,
            selectAll,
            clear,
            handleClickCancel,
        };
    },
};
</script>
