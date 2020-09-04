<template>
  <div :id="'reply-' + id" class="card mb-3">
    <div class="card-header" :class="isBest ? 'alert-success': ''">
      <div class="level">
        <h5 class="flex">
          <a :href="'/profiles/'+name" v-text="name"></a>
          said
          <span v-text="ago"></span>
          <!-- Moment JS can be used for date formatting. -->
        </h5>
        <div v-if="signedIn">
          <favorite :reply="data"></favorite>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div v-if="editing">
        <form @submit="update">
          <div class="form-group">
            <textarea class="form-control" v-model="body" required></textarea>
          </div>

          <button class="btn button-small btn-primary">Update</button>
          <button class="btn button-small btn-link" @click="editing = false" type="button">Cancel</button>
        </form>
      </div>
      <div v-else v-html="body"></div>
    </div>

    <div class="card-footer level" :class="isBest ? 'alert-success': ''">
      <div v-if="authorize('updateReply', reply)"> 
        <button class="btn btn-info button-small mr-1" @click="editing = true">Edit</button>
        <button class="btn btn-danger button-small mr-1" @click="destroy">Delete</button>
      </div>
      <button
        class="btn btn-primary button-small ml-auto"
        @click="markBestReply"
        v-show="! isBest"
      >Best Reply?</button>
    </div>
  </div>
</template>

<script>
import Favorite from "./Favorite.vue";
import moment from "moment";

export default {
  props: ["data"],

  components: { Favorite },

  data() {
    return {
      editing: false,
      id: this.data.id,
      name: this.data.owner.name,
      body: this.data.body,
      isBest: false,
      reply: this.data,
    };
  },

  computed: {
    ago() {
      return moment(this.data.created_at).fromNow() + "...";
    },
  },

  methods: {
    update() {
      axios
        .patch("/replies/" + this.data.id, {
          body: this.body,
        })
        .catch((error) => {
          flash(error.response.data, "danger");
        });

      this.editing = false;

      flash("Updated");
    },
    destroy() {
      axios.delete("/replies/" + this.data.id);

      //emit is a event
      this.$emit("deleted", this.data.id);
      // $(this.$el).fadeOut(300, () => {
      //     flash('Your reply has been deleted');
      // });
    },

    markBestReply() {
      this.isBest = true;
    },
  },
};
</script>
