<template>
    <div class="grid grid-cols-6 gap-4">
        <div class="field col-span-4">
            <label>Name:</label>
            <div class="data">
                {{ location.name }}
            </div>
        </div>
        <div class="field col-span-2">
            <label>Location Number/ID:</label>
            <div class="data">
                {{ location.location_no }}
            </div>
        </div>
        <div class="field col-span-2">
            <label>City:</label>
            <div class="data">
                {{ location.city }}
            </div>
        </div>
        <div class="field col-span-2">
            <label>State:</label>
            <div class="data">
                {{ location.state }}
            </div>
        </div>
        <div class="field col-span-2">
            <label>ZIP Code:</label>
            <div class="data">
                {{ location.zip }}
            </div>
        </div>

        <div class="flex flex-col col-span-6 space-y-2">
            <label>Address:</label>
            <div class="data">
                {{ location.address1 }}
            </div>
            <div class="data">
                {{ location.address2 }}
            </div>
        </div>
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
    props: ["location"],
};
</script>
