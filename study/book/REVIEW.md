# Review of the first seven book chapters

Date: 2026-11-06. Scope: original Persian teaching material for chapters 1–7 of the user's local 14-chapter PDF, following the prerequisite phase. The PDF itself is unchanged and is not included in the website payload. This is 50% by chapter count, not by pages, reading time, or the complete table of contents of other releases.

## Source and coverage

Source: Aurélien Géron, *Hands-On Machine Learning with Scikit-Learn, Keras, and TensorFlow*, second edition, 2019. The local file has 510 PDF pages and SHA-256 `423a91132cd25e4ffc5095ca4d0d5e4c99aa36d8747505dc1c1bbdfbf28ed94d`.

The title, copyright/edition page, table of contents, prerequisite statement and chapter text were inspected. The reading plan remains separate from the book's own numbering. References in lessons use printed page numbers.

| Chapter | Printed pages | Coverage |
| --- | --- | --- |
| 1 | 3–34 | Task types, batch/online, instance/model, data quality, over/underfitting, evaluation |
| 2 | 37–85 | Framing, metrics, splitting, EDA, preparation, features, pipelines, baseline, CV/search, monitoring |
| 3 | 87–110 | Digit representation, binary metrics, thresholds, ROC/PR, multiclass, errors, multilabel/multioutput |
| 4 | 113–153 | Least squares, numerical caveats, GD variants, scaling, polynomial features, regularization, early stopping, logistic/softmax |
| 5 | 155–175 | Margin, scaling, C/gamma, kernels, SVR, hinge/primal/dual interpretation, computational and online boundaries |
| 6 | 177–189 | Prediction paths, leaf frequencies, weighted impurity, entropy/CART, regularization, regression, instability |
| 7 | 191–213 | Hard/soft voting, bagging/pasting, OOB, feature subsampling, forests/extra trees, importance, boosting, stacking |

There are 58 teaching sections, 42 feedback questions, seven interactive labs, seven independent Python examples, seven runnable notebook files and seven optional library examples. Examples, exercises and diagrams are our own; they do not impersonate the book's exercise numbers or reported results.

## Corrections and clarity checks

- Imputation/scaling statistics belong to the training portion of each fold, not the complete dataset. The optional mixed-column pipeline uses `ColumnTransformer` and keeps preparation inside `GridSearchCV`.
- Validation selects models and thresholds; the independent final test is not reused for tuning.
- RMSE is in target units; signed errors must not cancel when reporting predictive error.
- Precision and recall denominators differ. Zero-denominator values are explicitly undefined in the browser; `zero_division=0` in the optional library example is documented as a reporting convention.
- Increasing a threshold does not guarantee monotonic precision. Recall is tested as nonincreasing for fixed labelled scores as the threshold rises.
- The optional bundled `digits` example is identified as 8×8, not the book's 28×28 MNIST data.
- A normal-equation inverse is not always defined. Training loss and generalization quality are separate.
- Lasso can set weights to zero; Ridge generally shrinks them. Early stopping uses validation.
- In the usual SVM convention, larger C penalizes violations more; larger RBF gamma is more local. Sum versus mean objectives rescale the numerical interpretation of C.
- Tree split impurity is weighted by child sample counts. Leaf frequencies are not guaranteed calibrated probabilities. A fixed seed is not a cure for statistical instability.
- OOB predictions use only models that omitted the evaluated row. The asymptotic 36.8% omission figure is not an exact count in each bootstrap sample.
- Stacking uses held-out/out-of-fold base predictions. Gradient boosting is not universally equivalent to fitting raw residuals for every loss.
- Full mathematical phrases use isolated LTR spans. Signed decimals and scientific notation were added to the shared formatter. English bibliography phrases are isolated as a unit.
- The website exporter now handles page links with fragment/query suffixes; the last chapter's continuation link no longer becomes a nonexistent file download.

## Verification boundaries

`node scripts/build-study.cjs` builds 53 native Vue pages, including the existing 45 pages and eight book pages. Node suites test the manifest, internal links, runtimes, chapter references, all lab mathematics, quiz feedback, note persistence/reset, blocked storage and preserved prerequisite data.

`python book/verify_examples.py` executes all seven independent examples and the code cells of all seven notebooks. It also parses every optional Scikit-Learn file. If scikit-learn is installed, the same runner executes the seven optional examples as well.

In this environment Scikit-Learn was not installed and installing from PyPI failed with TLS EOF/timeouts. Consequently the independent examples/notebooks have execution evidence, while optional library examples have syntax-check evidence only. This distinction is visible in the runner output; no claim of full library/runtime verification is made.

The contrast audit includes 45 authored colour pairs. Real browser desktop/mobile checks and release status are recorded in `STUDY.md`. A Git push is not by itself proof of a production deployment.
