<template>
	<div class="jet-ui_approve-disapprove">
		<button class="jet-ui_approve"
				@click="onApproveClick">
			<svg viewBox="0 0 64 64">
				<path d="M21.33,57.82,0,36.53l5.87-5.87L21.33,46.09,58.13,9.36,64,15.23,21.33,57.82" />
			</svg>
		</button>
		<button class="jet-ui_disapprove"
				@click="onDisapproveClick">
			<svg viewBox="0 0 20 20">
				<path d="M14.95 6.46L11.41 10l3.54 3.54-1.41 1.41L10 11.42l-3.53 3.53-1.42-1.42L8.58 10 5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z" />
			</svg>
		</button>
	</div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
	name: "ApproveDisapprove",

	props: {
	},

	setup(props, context) {
		// Actions
		const onApproveClick = () => {
			context.emit('approve');
		};

		const onDisapproveClick = () => {
			context.emit('disapprove');
		};

		return {
			onApproveClick,
			onDisapproveClick
		};
	}
});
</script>

<style lang="scss">
@import "../scss/controls/approve-disapprove.scss";
</style>
