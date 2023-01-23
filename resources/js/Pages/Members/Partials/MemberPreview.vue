<template>
    <div class="text-secondary font-bold mb-2">Record Preview</div>
    <div class="grid grid-cols-6 gap-4">
        <member-preview-item
            class="field col-span-6 md:col-span-3"
            label="Name"
            :value="data.member.first_name + ' ' + data.member.last_name"
        />
        <member-preview-item
            class="field col-span-6 md:col-span-3"
            label="Email"
            :value="data.member.email"
        />
        <member-preview-item
            class="field col-span-6 md:col-span-3"
            label="Phone 1"
            :value="data.member.phone"
        />
        <member-preview-item
            class="field col-span-6 md:col-span-3"
            label="Phone 2"
            :value="data.member.alternate_phone"
        />
        <member-preview-item
            class="field col-span-6 md:col-span-3"
            label="Gender"
            :value="data.member.gender"
        />
        <member-preview-item
            class="field col-span-6 md:col-span-3"
            label="Birthdate"
            :value="
                new Date(data.member.date_of_birth).toLocaleDateString('en-US')
            "
        />
        <member-preview-item
            class="field col-span-6 md:col-span-3"
            label="Contact"
            :value="data.member.email"
        />
        <div class="field col-span-6 md:col-span-3">
            <label>Contact</label>
            <div class="data">
                Called: {{ data.interactionCount.calledCount }} <br />
                Emailed: {{ data.interactionCount.emailedCount }} <br />
                Text: {{ data.interactionCount.smsCount }} <br />
            </div>
        </div>
        <div
            class="collapse col-span-6"
            tabindex="0"
            v-if="data?.preview_note?.length"
        >
            <div class="collapse-title text-sm font-medium">
                > Existing Notes
            </div>
            <div class="flex flex-col gap-2 collapse-content">
                <div v-for="(note, ndx) in data.preview_note" :key="ndx">
                    <div
                        class="text-sm text-base-content text-opacity-80 bg-base-100 rounded-lg p-2"
                    >
                        <hr
                            v-if="
                                ndx != 0 &&
                                data.preview_note[ndx - 1]['lifecycle'] !=
                                    note['lifecycle']
                            "
                            class="pb-5"
                        />
                        {{ note["note"] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="field col-span-6 md:col-span-3 text-secondary">
            <label>Club/ Location: {{ data.club_location.name }}</label>
        </div>
        <div class="field col-span-6 lg:col-span-6"></div>
    </div>
</template>
<style scoped>
input {
    @apply input-xs;
}

.field {
    @apply flex flex-col gap-2;
}
.data {
    @apply border border-secondary rounded text-sm p-3;
}
</style>

<script setup>
import MemberPreviewItem from "./MemberPreviewItem.vue";
const props = defineProps({
    data: {
        type: Object,
    },
});
</script>
