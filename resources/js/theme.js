import resolveConfig from "tailwindcss/resolveConfig";
import tailwindConfig from "tailwind.config.js"; // IMPORTANT that the path is NOT relative, only the file name

const fullConfig = resolveConfig(tailwindConfig);

export default {
    ...fullConfig.theme,
    daisyui: fullConfig.daisyui,
};
