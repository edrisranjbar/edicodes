"""Verify independent examples, notebooks and optional library-example syntax."""
from pathlib import Path
import ast
import contextlib
import io
import importlib.util
import json
import runpy
import sys

root = Path(__file__).resolve().parent
checked = []
optional_executed = 0
for chapter in range(1, 8):
    folder = root / f"chapter-{chapter:02d}" / "resources"
    namespace = runpy.run_path(str(folder / "example.py"))
    result = namespace["run_demo"]()
    ast.parse((folder / "scikit_example.py").read_text(encoding="utf-8"))
    notebook = json.loads((folder / "exercises.ipynb").read_text(encoding="utf-8"))
    env = {"__name__": "__main__"}
    with contextlib.redirect_stdout(io.StringIO()):
        for cell in notebook["cells"]:
            if cell["cell_type"] == "code":
                exec(compile("".join(cell["source"]), f"chapter-{chapter}.ipynb", "exec"), env)
    checked.append({"chapter": chapter, "result": result})
    if importlib.util.find_spec("sklearn") is not None:
        with contextlib.redirect_stdout(io.StringIO()):
            runpy.run_path(str(folder / "scikit_example.py"), run_name="__main__")
        optional_executed += 1
print(json.dumps({"independent_examples": 7, "executed_notebooks": 7,
                  "optional_scikit_syntax_checked": 7,
                  "optional_scikit_executed": optional_executed, "results": checked}))
