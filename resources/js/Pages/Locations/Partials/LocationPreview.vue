<template>
    <div class="grid grid-cols-6 gap-6">
        <div class="col-span-4">
            <jet-label for="name" value="Name" />
            <input
                id="name"
                type="text"
                class="block w-full mt-1"
                :value="location.name"
            />
        </div>
        <div class="col-span-2">
            <jet-label for="location_no" value="Location Number/ID" />
            <input
                id="location_no"
                type="text"
                class="block w-full mt-1"
                :value="location.location_no"
            />
        </div>
        <div class="col-span-4">
            <jet-label for="city" value="City" />
            <input
                id="city"
                type="text"
                class="block w-full mt-1"
                :value="location.city"
            />
        </div>
        <div class="col-span-1">
            <jet-label for="state" value="State" />
            <input
                id="state"
                type="text"
                class="block w-full mt-1"
                :value="location.state"
            />
        </div>
        <div class="col-span-1">
            <jet-label for="zip" value="ZIP Code" />
            <input
                id="zip"
                type="text"
                class="block w-full mt-1"
                :value="location.zip"
            />
        </div>

        <div class="col-span-6 space-y-2">
            <jet-label for="address1" value="Address" />
            <input
                id="address1"
                type="text"
                class="block w-full mt-1"
                :value="location.address1"
            />
            <input
                id="address2"
                type="text"
                class="block w-full mt-1"
                :value="location.address2"
            />
        </div>

<!--        <div class="col-span-2 space-y-2">-->
<!--            <jet-label for="phone" value="Phone" />-->
<!--            <input-->
<!--                id="phone"-->
<!--                type="text"-->
<!--                class="block w-full mt-1"-->
<!--                :value="location.phone"-->
<!--            />-->
<!--        </div>-->

<!--        <div class="col-span-2 space-y-2">-->
<!--            <jet-label for="opendate" value="Open Date" />-->
<!--            <input-->
<!--                id="opendate"-->
<!--                type="text"-->
<!--                class="block w-full mt-1"-->
<!--                :value="location.opendate"-->
<!--            />-->
<!--        </div>-->
<!--        <div class="col-span-2 space-y-2">-->
<!--            <jet-label for="closedate" value="Close Date" />-->
<!--            <input-->
<!--                id="closedate"-->
<!--                type="text"-->
<!--                class="block w-full mt-1"-->
<!--                :value="location.closedate"-->
<!--            />-->
<!--        </div>-->

<!--        <div class="col-span-2 space-y-2">-->
<!--            <jet-label for="poc_first" value="POC First" />-->
<!--            <input-->
<!--                id="poc_first"-->
<!--                type="text"-->
<!--                class="block w-full mt-1"-->
<!--                :value="location.poc_first"-->
<!--            />-->
<!--        </div>-->
<!--        <div class="col-span-2 space-y-2">-->
<!--            <jet-label for="poc_last" value="POC Last" />-->
<!--            <input-->
<!--                id="poc_last"-->
<!--                type="text"-->
<!--                class="block w-full mt-1"-->
<!--                :value="location.poc_last"-->
<!--            />-->
<!--        </div>-->
<!--        <div class="col-span-2 space-y-2">-->
<!--            <jet-label for="poc_phone" value="POC Phone" />-->
<!--            <input-->
<!--                id="poc_phone"-->
<!--                type="text"-->
<!--                class="block w-full mt-1"-->
<!--                :value="location.poc_phone"-->
<!--            />-->
<!--        </div>-->
    </div>
</template>

<script>
import JetLabel from "@/Jetstream/Label";

export default {
    components: {
        JetLabel,
    },
    props: ["location"],
};
</script>
