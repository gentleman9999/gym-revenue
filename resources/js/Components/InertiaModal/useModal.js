import { ref, watchEffect } from "vue";

const modal = ref(null);

export default () => modal;

//trying to get the "x" on top right of modal to fire off onClose
// watchEffect(() => {
//     console.log('adding the onclose callback')
//     if (modal?.value && !modal.value?.onClose) {
//         modal.value.onClose = () => {
//             console.log("click on close");
//         };
//     }
// });
