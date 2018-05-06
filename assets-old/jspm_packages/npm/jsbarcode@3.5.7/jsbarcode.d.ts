declare function JsBarcode(element: any): any;
declare function JsBarcode(element: any, data: string, options?: jsbarcode.JsBarcodeOptions): jsbarcode.api;

declare namespace jsbarcode {
  interface JsBarcodeOptions {
    width?: number,
    height?: number,
    format?: string,
    displayValue?: boolean,
    fontOptions?: string,
    font?: string,
    text?: string,
    textAlign?: string,
    textPosition?: string,
    textMargin?: number,
    fontSize?: number,
    background?: string,
    lineColor?: string,
    margin?: number,
    marginTop?: number,
    marginBottom?: number,
    marginLeft?: number,
    marginRight?: number,
    valid?: Function
  }

  interface api {
    options(options: JsBarcodeOptions): api;
    blank(size: number): api;
    init(options): void;
    render(): void;
  }
}

export = JsBarcode;
