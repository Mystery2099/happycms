import type { Component } from "svelte";

export interface DropdownOption {
  value: string;
  label: string;
  icon?: Component;
}
