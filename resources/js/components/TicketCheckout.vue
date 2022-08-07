<template>
  <div>
    <div class="d-flex justify-content-between align-items-center w-full">
      <div>
        <h4 class="form-group mt-4 font-weight-bold">
          <label class="form-label">
            Price
          </label>
          <span class="form-control-static">
            ${{ totalPriceInDollars }}
          </span>
        </h4>
      </div>
      <div class="w-25">
        <div class="form-group">
            <label class="form-label" for="quantity">
              Qty
            </label>
            <input v-model="quantity" class="form-control" id="quantity">
        </div>
      </div>
    </div>
    <div class="border p-2 rounded">
      <div id="payment-card"></div>
    </div>
    <div class="text-center w-full mt-4">
      <div class="spinner-border text-primary" role="status" v-if="processing">
        <span class="sr-only">Loading...</span>
      </div>
      <button class="btn btn-primary btn-block"
              :class="{ 'btn-loading': processing }"
              :disabled="processing"
              v-else
              @click="processPayment()"
      >
        Buy Tickets
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: [
      'price',
      'concertTitle',
      'concertId',
  ],

  data() {
    return {
        quantity: 1,
        processing: false,
        stripe: null,
        card: null,
        cardErrors: null
    };
  },

  computed: {
    totalPrice() {
        return this.quantity * this.price;
    },

    priceInDollars() {
        return (this.price / 100).toFixed(2);
    },

    totalPriceInDollars() {
        return (this.totalPrice / 100).toFixed(2);
    },
  },

  mounted() {
    this.stripe = Stripe(process.env.MIX_STRIPE_KEY);

    this.inputElements();
  },

  methods: {
    inputElements() {
      var elements = this.stripe.elements({
        fonts: [
          {
            cssSrc: 'https://fonts.googleapis.com/css?family=Roboto',
          },
        ],
        locale: 'auto'
      });

      const card = elements.create('card', {
        iconStyle: 'solid',
        style: {
          base: {
            iconColor: '##5e7fe4',
            color: '#000',
            fontWeight: 500,
            fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
            fontSize: '16px',
            fontSmoothing: 'antialiased',
          },
          invalid: {
            iconColor: '#d03e3e',
            color: '#d03e3e',
          },
        },
      });

      this.card = card;
      card.mount('#payment-card');
    },

    async processPayment() {
      this.processing = true;
      paymentToken = null;

      try {
        const result = await this.stripe.createToken(this.card);
        if (result.error) {
          this.cardErrors = result.error.message;
        }
        paymentToken = result.token.id;
      } catch(error) {
        console.log(error);
      }

      this.processing = false;
    }
  }
};
</script>