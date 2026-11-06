// Optional library exercises. The core downloadable examples have no third-party dependencies.
const header=`"""تمرین تکمیلی Scikit-Learn؛ دادهٔ همراه کتابخانه یا دادهٔ مصنوعی.
این نمونه‌ها بازنویسی آموزشی‌اند، نه کد یا نتیجهٔ عددی کتاب.
به numpy و scikit-learn>=1.2,<2 نیاز دارد.
"""\n`;
module.exports=[
header+`from sklearn.datasets import load_iris
from sklearn.model_selection import train_test_split
from sklearn.neighbors import KNeighborsClassifier
from sklearn.pipeline import make_pipeline
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import accuracy_score

X, y = load_iris(return_X_y=True)
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.25, random_state=42, stratify=y)
model = make_pipeline(StandardScaler(), KNeighborsClassifier(n_neighbors=5))
model.fit(X_train, y_train)
print("test_accuracy", accuracy_score(y_test, model.predict(X_test)))
`,
header+`import numpy as np
import pandas as pd
from sklearn.compose import ColumnTransformer
from sklearn.datasets import make_regression
from sklearn.model_selection import train_test_split, GridSearchCV
from sklearn.impute import SimpleImputer
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler, OneHotEncoder
from sklearn.linear_model import Ridge
from sklearn.dummy import DummyRegressor
from sklearn.metrics import mean_squared_error

X, y = make_regression(n_samples=240, n_features=4, noise=12, random_state=42)
X[::11, 0] = np.nan
X = pd.DataFrame(X, columns=["size", "distance", "age", "rooms"])
X["area"] = np.where(np.arange(len(X)) % 2, "A", "B")
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42)
numeric = Pipeline([("fill", SimpleImputer(strategy="median")),
                    ("scale", StandardScaler())])
prepare = ColumnTransformer([
    ("numeric", numeric, ["size", "distance", "age", "rooms"]),
    ("category", OneHotEncoder(handle_unknown="ignore"), ["area"]),
])
pipeline = Pipeline([("prepare", prepare), ("model", Ridge(solver="lsqr"))])
search = GridSearchCV(pipeline, {"model__alpha": [0.1, 1.0, 10.0]},
                      scoring="neg_mean_squared_error", cv=4)
search.fit(X_train, y_train)
baseline = DummyRegressor(strategy="mean").fit(np.zeros((len(X_train), 1)), y_train)
print("best_parameters", search.best_params_)
print("test_rmse", mean_squared_error(y_test, search.predict(X_test)) ** 0.5)
print("baseline_rmse", mean_squared_error(y_test, baseline.predict(np.zeros((len(X_test), 1)))) ** 0.5)
`,
header+`from sklearn.datasets import load_digits
from sklearn.model_selection import train_test_split
from sklearn.pipeline import make_pipeline
from sklearn.preprocessing import StandardScaler
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import confusion_matrix, precision_score, recall_score

# digits شامل تصویرهای ۸ در ۸ است و MNIST کتاب نیست.
X, digits = load_digits(return_X_y=True)
y = (digits == 5).astype(int)
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.25, random_state=42, stratify=y)
model = make_pipeline(StandardScaler(), LogisticRegression(max_iter=2000))
model.fit(X_train, y_train)
predicted = model.predict(X_test)
print("confusion_rows_true_columns_predicted", confusion_matrix(y_test, predicted))
# صفر در مخرج با قرارداد نرم‌افزاری صفر گزارش می‌شود.
print("precision", precision_score(y_test, predicted, zero_division=0))
print("recall", recall_score(y_test, predicted, zero_division=0))
`,
header+`from sklearn.datasets import make_regression
from sklearn.model_selection import train_test_split, GridSearchCV
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler, PolynomialFeatures
from sklearn.linear_model import Ridge
from sklearn.metrics import mean_squared_error

X, y = make_regression(n_samples=220, n_features=2, noise=15, random_state=42)
X_train, X_test, y_train, y_test = train_test_split(X, y, random_state=42)
model = Pipeline([("poly", PolynomialFeatures(include_bias=False)),
                  ("scale", StandardScaler()), ("ridge", Ridge())])
search = GridSearchCV(model, {"poly__degree": [1, 2, 3],
                              "ridge__alpha": [0.1, 1.0, 10.0]},
                      cv=4, scoring="neg_mean_squared_error")
search.fit(X_train, y_train)
print("best_parameters", search.best_params_)
print("test_rmse", mean_squared_error(y_test, search.predict(X_test)) ** 0.5)
`,
header+`from sklearn.datasets import make_moons
from sklearn.model_selection import train_test_split, GridSearchCV
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler
from sklearn.svm import SVC
from sklearn.metrics import accuracy_score

X, y = make_moons(n_samples=220, noise=0.22, random_state=42)
X_train, X_test, y_train, y_test = train_test_split(
    X, y, random_state=42, stratify=y)
model = Pipeline([("scale", StandardScaler()), ("svm", SVC(kernel="rbf"))])
search = GridSearchCV(model, {"svm__C": [0.1, 1.0, 10.0],
                              "svm__gamma": [0.1, 1.0, 10.0]}, cv=4)
search.fit(X_train, y_train)
print("best_parameters", search.best_params_)
print("test_accuracy", accuracy_score(y_test, search.predict(X_test)))
`,
header+`from sklearn.datasets import load_iris
from sklearn.model_selection import train_test_split, GridSearchCV
from sklearn.tree import DecisionTreeClassifier, export_text
from sklearn.metrics import accuracy_score

data = load_iris()
X_train, X_test, y_train, y_test = train_test_split(
    data.data, data.target, random_state=42, stratify=data.target)
search = GridSearchCV(DecisionTreeClassifier(random_state=42),
                      {"max_depth": [2, 3, 4], "min_samples_leaf": [1, 3, 5]}, cv=4)
search.fit(X_train, y_train)
print(export_text(search.best_estimator_, feature_names=list(data.feature_names)))
print("test_accuracy", accuracy_score(y_test, search.predict(X_test)))
`,
header+`from sklearn.datasets import load_iris
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.tree import DecisionTreeClassifier
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.metrics import accuracy_score

X, y = load_iris(return_X_y=True)
X_train, X_test, y_train, y_test = train_test_split(
    X, y, random_state=42, stratify=y)
models = {
    "tree": DecisionTreeClassifier(max_depth=3, random_state=42),
    "forest": RandomForestClassifier(n_estimators=80, random_state=42),
    "boosting": GradientBoostingClassifier(n_estimators=60, random_state=42),
}
scores = {name: cross_val_score(model, X_train, y_train, cv=4).mean()
          for name, model in models.items()}
chosen = max(scores, key=scores.get)
model = models[chosen].fit(X_train, y_train)
print("validation_scores", scores)
print("chosen_model", chosen)
print("final_test_accuracy", accuracy_score(y_test, model.predict(X_test)))
`];
