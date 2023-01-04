<template>
    <jet-form-section @submitted="handleSubmit">
        <template #title> Team Details</template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
            <div class="grid-col-full">
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

            <div class="grid-col-full">
                <LocationSelect v-model="form.locations" />
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

<style scoped>
.grid-col-full {
    grid-column: 1 / -1;
}
</style>

<script setup>
import * as _ from "lodash";
import mutations from "@/gql/mutations";
import { useGymRevForm } from "@/utils";
import { useMutation } from "@vue/apollo-composable";

import LocationSelect from "@/Pages/Locations/Partials/LocationSelect.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

const props = defineProps({
    team: {
        type: Object,
    },
});

const emit = defineEmits(["close"]);

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
</script>
