import { inject } from "vue";
import { injectIsModal } from "./symbols";

export default () => inject(injectIsModal, false);
