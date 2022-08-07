<template>
  <div>
    <div v-if="paymentDropdown">
      <div class="d-flex justify-content-between align-items-center w-full">
        <div>
          <h4 class="form-group mt-4 font-weight-bold">
            <label class="form-label">
              Total:
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
              <input class="form-control" 
                     id="quantity"
                     v-model="quantity">
          </div>
        </div>
      </div>
      <p class="text-danger mt-1">{{ errorFor('ticket_quantity') }}</p>
      <div class="my-2">
        <div class="form-group">
            <input v-model="email" class="form-control" id="email" placeholder="Email">
            <p class="text-danger mt-1">{{ errorFor('email') }}</p>
        </div>
      </div>
      <div class="border p-2 rounded">
        <div id="payment-card"></div>
      </div>
      <h6 class="text-danger mt-2" v-if="stripeError">{{ stripeError }}</h6>
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
    <div class="mt-4" v-else>
      <button class="btn btn-primary btn-block"
              @click="openPaymentDropdown()">
        Enter Payment
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
        paymentDropdown: false,
        quantity: 1,
        email: null,
        processing: false,
        stripe: null,
        cardElement: null,
        paymentToken: null,
        stripeError: null,
        validationErrors: null
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

      this.cardElement = card;
      card.mount('#payment-card');
    },

    async processPayment() {
      this.processing = true;
      this.stripeError = null;
      this.validationErrors = null;

      try {
        const result = await this.stripe.createToken(this.cardElement);

        if (result.error) {
          this.stripeError = result.error.message;
          this.processing = false;
          return;
        } else {
          this.paymentToken = result.token.id;
        }
      } catch(error) {
        console.log(error);
        this.processing = false;
        return;
      }

      try {
        const result = await axios.post(`/concerts/${this.concertId}/orders`, {
          email: this.email,
          ticket_quantity: this.quantity,
          payment_token: this.paymentToken
        });

        this.paymentDropdown = false;

        alert('Tickets have been purchased!');
      } catch(error) {
        if (error.response.status === 422) {
          this.validationErrors = error.response.data.errors;
          console.log('Validation errors');
        } else {
          console.log('General error');
        }
      }

      this.processing = false;
    },

    openPaymentDropdown() {
      this.paymentDropdown = true;
      setTimeout(() => {
        this.inputElements();
      }, 500);
    },

    errorFor(field) {
      if (this.validationErrors !== null && this.validationErrors[field]) {
        return this.validationErrors[field][0];
      }
      return null;
    },
  }
};
</script>