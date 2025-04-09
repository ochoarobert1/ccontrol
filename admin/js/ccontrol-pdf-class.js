class jsPDFCreator {
  stepReader(pdf, step) {
    switch (step[0]) {
      case "addPage":
        pdf.addPage();
        pdf.setPage(step[1]);
        break;
      case "text":
        pdf.text(step[1][0], step[1][1], step[1][2], { maxWidth: 410 });
        break;

      case "addImage":
        pdf.addImage(
          step[1][0],
          step[1][1],
          step[1][2],
          step[1][3],
          step[1][4],
          step[1][5]
        );
        break;

      case "setFont":
        pdf.setFont(step[1].toString());
        break;

      case "setFontSize":
        pdf.setFontSize(step[1].toString());
        break;

      case "addFont":
        pdf.setFontSize(step[1].toString());
        break;

      default:
        break;
    }

    return pdf;
  }
}
