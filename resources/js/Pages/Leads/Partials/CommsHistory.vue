<template>
    <ul class="w-full steps">
        <CommsHistoryStep v-for="detail in details" :detail="detail" @click="selectedDetail = detail"/>
    </ul>
    <CommsHistoryDetail :detail="selectedDetail" v-if="selectedDetail"/>
    <span v-else class="block py-8 w-full text-center self-center m-auto opacity-50 text-xl">Click on an item above to learn more</span>
</template>
<script>
import {ref} from "vue";
import CommsHistoryStep from "./CommsHistoryStep";
import CommsHistoryDetail from "./CommsHistoryDetail";

export default {
    name: 'CommsHistory',
    components: {
        CommsHistoryDetail,
        CommsHistoryStep
    },
    props: {
        details: {
            type: Array
        }
    },
    setup(props) {
        const selectedDetail = ref(null);

        return {selectedDetail}
    }
}
</script>
