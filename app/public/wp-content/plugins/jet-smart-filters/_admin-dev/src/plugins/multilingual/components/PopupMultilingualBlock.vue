<template >
	<div class="jet-popup-form">
		<ML_Block :filterID="filterID" />
	</div>
</template>

<script>
import { defineComponent } from "vue";
import popup from "@/services/popups.js";

export default defineComponent({
	name: 'PopupMultilingualBlock',

	setup(props, context) {
		const filterID = popup.data.value;

		return {
			filterID
		};
	}
});
</script>