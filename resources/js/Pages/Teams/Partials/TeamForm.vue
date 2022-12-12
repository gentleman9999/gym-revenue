<template>
    <jet-form-section @submitted="handleSubmit">
        <template #title> Team Details</template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Team Name" />
                <input
                    id="name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.name"
                    autofocus
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <div
                class="col-span-6 sm:col-span-4"
                v-if="availableLocations?.data.length"
            >
                <jet-label for="locations" value="Locations" />
                <multiselect
                    v-model="form.locations"
                    id="locations"
                    mode="tags"
                    :close-on-select="false"
                    :create-option="true"
                    :options="
                        availableLocations.data.map((location) => ({
                            label: location.name,
                            value: location.id,
                        }))
                    "
                    :classes="multiselectClasses"
                />
                <jet-input-error
                    :message="form.errors.locations"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>
            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
                primary
            >
                Save
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import * as _ from "lodash";
import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations";

export default defineComponent({
    components: {
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        JetActionMessage,
        Multiselect,
    },
    props: {
        team: {
            type: Object,
        },
        availableLocations: {
            type: Object,
            required: true,
        },
    },

    setup(props, { emit }) {
        const page = usePage();
        let operation = "Update";
        let team = _.cloneDeep(props.team);
        if (!team) {
            team = {
                name: "",
                locations: [],
            };
            operation = "Create";
        } else {
            team.locations = team.locations.map((detail) => detail.value);
        }
        const form = useGymRevForm(team);

        const { mutate: createTeam } = useMutation(mutations.team.create);
        const { mutate: updateTeam } = useMutation(mutations.team.update);

        let handleSubmit = async () => {
            await updateTeam({
                id: team.id,
                name: form.name,
                positions: form.positions,
            });
            emit("close");
        };
        if (operation === "Create") {
            handleSubmit = async () => {
                await createTeam({
                    name: form.name,
                    positions: form.positions,
                });
                emit("close");
            };
        }

        return {
            form,
            operation,
            handleSubmit,
            page,
            multiselectClasses: getDefaultMultiselectTWClasses(),
        };
    },
});
</script>
