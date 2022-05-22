<template>
    <div class="grid grid-cols-6 gap-4">
        <div class="field col-span-6 lg:col-span-3">
            <label>Name:</label>
            <div class="data">
                {{ data.member.first_name }}
                {{ data.member.last_name }}
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Email:</label>
            <div class="data">
                {{ data.member.email }}
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Phone:</label>
            <div class="data">
                {{ data.member.primary_phone }}
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Phone secondary:</label>
            <div class="data" v-if="data.member.alternate_phone">
                {{ data.member.alternate_phone }}
            </div>
            <div class="data" v-if="!data.member.alternate_phone">
                Not Available
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Gender:</label>
            <div class="data" v-if="data.member.gender">
                {{ data.member.gender }}
            </div>
        </div>
        <div
            class="field col-span-6 lg:col-span-3"
            v-if="data.member?.dob?.value"
        >
            <label>Birthdate:</label>
            <div class="data" v-if="data.member.dob">
                {{
                    new Date(data.member.dob.value).toLocaleDateString("en-US")
                }}
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Contact:</label>
            <div class="data">
                Called: {{ data.interactionCount.calledCount }} <br />
                Emailed: {{ data.interactionCount.emailedCount }} <br />
                Text: {{ data.interactionCount.smsCount }} <br />
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Club/Location:</label>
            <div class="data">{{ data.club_location.name }}</div>
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
                <div
                    v-for="note in data.preview_note"
                    class="text-sm text-base-content text-opacity-80 bg-base-100 rounded-lg p-2"
                >
                    {{ note["note"] }}
                </div>
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-6"></div>
    </div>
</template>
<style scoped>
input {
    @apply input-xs;
}

.field {
    @apply flex flex-row gap-2;
}
</style>

<script>
export default {
    props: ["data", "member", "club", "note", "notes"],
};
</script>
