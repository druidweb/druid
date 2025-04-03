import { defineComponent, computed, unref, mergeProps, withCtx, renderSlot, createVNode, useSSRContext, createTextVNode, createBlock, createCommentVNode, openBlock, toDisplayString, withModifiers } from "vue";
import { ssrRenderComponent, ssrRenderSlot, ssrInterpolate } from "vue/server-renderer";
import { _ as _sfc_main$3, a as _sfc_main$5 } from "./Label-i2VVf5Sf.js";
import { _ as _sfc_main$6 } from "./TextLink-D0fLoZqL.js";
import { c as cn, _ as _sfc_main$7 } from "./AppLogoIcon-xe2zDqH9.js";
import { Check, LoaderCircle } from "lucide-vue-next";
import { useForwardPropsEmits, CheckboxRoot, CheckboxIndicator } from "radix-vue";
import { _ as _sfc_main$4 } from "./Input-D5Mz7eRZ.js";
import { _ as _sfc_main$2 } from "./AuthLayout-BFciNwP-.js";
import { useForm, Head } from "@inertiajs/vue3";
import { u as useRoutes } from "./useRoutes-DcRJSvd8.js";
import "clsx";
import "tailwind-merge";
import "class-variance-authority";
import "@vueuse/core";
import "../app.js";
import "laravel-echo";
import "pusher-js";
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "Checkbox",
  __ssrInlineRender: true,
  props: {
    defaultChecked: { type: Boolean },
    checked: { type: [Boolean, String] },
    disabled: { type: Boolean },
    required: { type: Boolean },
    name: {},
    value: {},
    id: {},
    asChild: { type: Boolean },
    as: {},
    class: {}
  },
  emits: ["update:checked"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const emits = __emit;
    const delegatedProps = computed(() => {
      const { class: _, ...delegated } = props;
      return delegated;
    });
    const forwarded = useForwardPropsEmits(delegatedProps, emits);
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(CheckboxRoot), mergeProps(unref(forwarded), {
        class: unref(cn)(
          "focus-visible:outline-hidden peer size-5 shrink-0 rounded-sm border border-input ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:border-accent-foreground data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground",
          props.class
        )
      }, _attrs), {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(CheckboxIndicator), { class: "flex h-full w-full items-center justify-center text-current" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  ssrRenderSlot(_ctx.$slots, "default", {}, () => {
                    _push3(ssrRenderComponent(unref(Check), { class: "size-3.5 stroke-[3]" }, null, _parent3, _scopeId2));
                  }, _push3, _parent3, _scopeId2);
                } else {
                  return [
                    renderSlot(_ctx.$slots, "default", {}, () => [
                      createVNode(unref(Check), { class: "size-3.5 stroke-[3]" })
                    ])
                  ];
                }
              }),
              _: 3
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(unref(CheckboxIndicator), { class: "flex h-full w-full items-center justify-center text-current" }, {
                default: withCtx(() => [
                  renderSlot(_ctx.$slots, "default", {}, () => [
                    createVNode(unref(Check), { class: "size-3.5 stroke-[3]" })
                  ])
                ]),
                _: 3
              })
            ];
          }
        }),
        _: 3
      }, _parent));
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/components/ui/checkbox/Checkbox.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "Login",
  __ssrInlineRender: true,
  props: {
    status: {},
    canResetPassword: { type: Boolean }
  },
  setup(__props) {
    const form = useForm({
      email: "",
      password: "",
      remember: false
    });
    const submit = () => {
      form.post(useRoutes("login"), {
        onFinish: () => form.reset("password")
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$2, mergeProps({
        title: "Log in to your account",
        description: "Enter your email and password below to log in"
      }, _attrs), {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(Head), { title: "Log in" }, null, _parent2, _scopeId));
            if (_ctx.status) {
              _push2(`<div class="mb-4 text-center text-sm font-medium text-green-600"${_scopeId}>${ssrInterpolate(_ctx.status)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<form class="flex flex-col gap-6"${_scopeId}><div class="grid gap-6"${_scopeId}><div class="grid gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(_sfc_main$3), { for: "email" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`Email address`);
                } else {
                  return [
                    createTextVNode("Email address")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(_sfc_main$4), {
              id: "email",
              type: "email",
              required: "",
              autofocus: "",
              tabindex: "1",
              autocomplete: "email",
              modelValue: unref(form).email,
              "onUpdate:modelValue": ($event) => unref(form).email = $event,
              placeholder: "email@example.com"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              message: unref(form).errors.email
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="grid gap-2"${_scopeId}><div class="flex items-center justify-between"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(_sfc_main$3), { for: "password" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`Password`);
                } else {
                  return [
                    createTextVNode("Password")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            if (_ctx.canResetPassword) {
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("password.request"),
                class: "text-sm",
                tabindex: "5"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(` Forgot password? `);
                  } else {
                    return [
                      createTextVNode(" Forgot password? ")
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            _push2(ssrRenderComponent(unref(_sfc_main$4), {
              id: "password",
              type: "password",
              required: "",
              tabindex: "2",
              autocomplete: "current-password",
              modelValue: unref(form).password,
              "onUpdate:modelValue": ($event) => unref(form).password = $event,
              placeholder: "Password"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              message: unref(form).errors.password
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center justify-between" tabindex="3"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(_sfc_main$3), {
              for: "remember",
              class: "flex items-center space-x-3"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(_sfc_main$1), {
                    id: "remember",
                    checked: unref(form).remember,
                    "onUpdate:checked": ($event) => unref(form).remember = $event,
                    tabindex: "4"
                  }, null, _parent3, _scopeId2));
                  _push3(`<span${_scopeId2}>Remember me</span>`);
                } else {
                  return [
                    createVNode(unref(_sfc_main$1), {
                      id: "remember",
                      checked: unref(form).remember,
                      "onUpdate:checked": ($event) => unref(form).remember = $event,
                      tabindex: "4"
                    }, null, 8, ["checked", "onUpdate:checked"]),
                    createVNode("span", null, "Remember me")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(unref(_sfc_main$7), {
              type: "submit",
              class: "mt-4 w-full",
              tabindex: "4",
              disabled: unref(form).processing
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  if (unref(form).processing) {
                    _push3(ssrRenderComponent(unref(LoaderCircle), { class: "h-4 w-4 animate-spin" }, null, _parent3, _scopeId2));
                  } else {
                    _push3(`<!---->`);
                  }
                  _push3(` Log in `);
                } else {
                  return [
                    unref(form).processing ? (openBlock(), createBlock(unref(LoaderCircle), {
                      key: 0,
                      class: "h-4 w-4 animate-spin"
                    })) : createCommentVNode("", true),
                    createTextVNode(" Log in ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div><div class="text-center text-sm text-muted-foreground"${_scopeId}> Don&#39;t have an account? `);
            _push2(ssrRenderComponent(_sfc_main$6, {
              href: _ctx.route("register"),
              tabindex: 5
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`Sign up`);
                } else {
                  return [
                    createTextVNode("Sign up")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></form>`);
          } else {
            return [
              createVNode(unref(Head), { title: "Log in" }),
              _ctx.status ? (openBlock(), createBlock("div", {
                key: 0,
                class: "mb-4 text-center text-sm font-medium text-green-600"
              }, toDisplayString(_ctx.status), 1)) : createCommentVNode("", true),
              createVNode("form", {
                onSubmit: withModifiers(submit, ["prevent"]),
                class: "flex flex-col gap-6"
              }, [
                createVNode("div", { class: "grid gap-6" }, [
                  createVNode("div", { class: "grid gap-2" }, [
                    createVNode(unref(_sfc_main$3), { for: "email" }, {
                      default: withCtx(() => [
                        createTextVNode("Email address")
                      ]),
                      _: 1
                    }),
                    createVNode(unref(_sfc_main$4), {
                      id: "email",
                      type: "email",
                      required: "",
                      autofocus: "",
                      tabindex: "1",
                      autocomplete: "email",
                      modelValue: unref(form).email,
                      "onUpdate:modelValue": ($event) => unref(form).email = $event,
                      placeholder: "email@example.com"
                    }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                    createVNode(_sfc_main$5, {
                      message: unref(form).errors.email
                    }, null, 8, ["message"])
                  ]),
                  createVNode("div", { class: "grid gap-2" }, [
                    createVNode("div", { class: "flex items-center justify-between" }, [
                      createVNode(unref(_sfc_main$3), { for: "password" }, {
                        default: withCtx(() => [
                          createTextVNode("Password")
                        ]),
                        _: 1
                      }),
                      _ctx.canResetPassword ? (openBlock(), createBlock(_sfc_main$6, {
                        key: 0,
                        href: _ctx.route("password.request"),
                        class: "text-sm",
                        tabindex: "5"
                      }, {
                        default: withCtx(() => [
                          createTextVNode(" Forgot password? ")
                        ]),
                        _: 1
                      }, 8, ["href"])) : createCommentVNode("", true)
                    ]),
                    createVNode(unref(_sfc_main$4), {
                      id: "password",
                      type: "password",
                      required: "",
                      tabindex: "2",
                      autocomplete: "current-password",
                      modelValue: unref(form).password,
                      "onUpdate:modelValue": ($event) => unref(form).password = $event,
                      placeholder: "Password"
                    }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                    createVNode(_sfc_main$5, {
                      message: unref(form).errors.password
                    }, null, 8, ["message"])
                  ]),
                  createVNode("div", {
                    class: "flex items-center justify-between",
                    tabindex: "3"
                  }, [
                    createVNode(unref(_sfc_main$3), {
                      for: "remember",
                      class: "flex items-center space-x-3"
                    }, {
                      default: withCtx(() => [
                        createVNode(unref(_sfc_main$1), {
                          id: "remember",
                          checked: unref(form).remember,
                          "onUpdate:checked": ($event) => unref(form).remember = $event,
                          tabindex: "4"
                        }, null, 8, ["checked", "onUpdate:checked"]),
                        createVNode("span", null, "Remember me")
                      ]),
                      _: 1
                    })
                  ]),
                  createVNode(unref(_sfc_main$7), {
                    type: "submit",
                    class: "mt-4 w-full",
                    tabindex: "4",
                    disabled: unref(form).processing
                  }, {
                    default: withCtx(() => [
                      unref(form).processing ? (openBlock(), createBlock(unref(LoaderCircle), {
                        key: 0,
                        class: "h-4 w-4 animate-spin"
                      })) : createCommentVNode("", true),
                      createTextVNode(" Log in ")
                    ]),
                    _: 1
                  }, 8, ["disabled"])
                ]),
                createVNode("div", { class: "text-center text-sm text-muted-foreground" }, [
                  createTextVNode(" Don't have an account? "),
                  createVNode(_sfc_main$6, {
                    href: _ctx.route("register"),
                    tabindex: 5
                  }, {
                    default: withCtx(() => [
                      createTextVNode("Sign up")
                    ]),
                    _: 1
                  }, 8, ["href"])
                ])
              ], 32)
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/pages/auth/Login.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
